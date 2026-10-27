<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use App\Services\SeguimientoEsam\SenderAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Envía recordatorios de cuotas y avisos de notas por Seguimiento ESAM.
 */
class WhatsappEnvioController extends Controller
{
    /** Cobranzas de un programa (vencidas o próximas). */
    public function enviarContable(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programa_id' => 'required|integer',
            'modo' => 'required|in:vencidas,proximas',
            'dias' => 'nullable|integer|min:1|max:180',
            'participante_id' => 'nullable|integer',
            'dry_run' => 'nullable|boolean',
        ]);

        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        [$account, $accountError] = SenderAccount::resolve();

        if ($accountError) {
            return response()->json(['success' => false, 'message' => $accountError], 403);
        }

        $builder = app(\App\Services\Cobranzas\RecordatorioCobranza::class);
        $data = $builder->construir((int) $validated['programa_id'], $validated['modo'], $validated['participante_id'] ?? null);

        if (! $data['programa']) {
            return response()->json(['success' => false, 'message' => 'Programa no encontrado'], 404);
        }

        if ($data['items'] === []) {
            return response()->json(['success' => false, 'message' => 'No hay cuotas pendientes para ese programa.'], 404);
        }

        $dryRun = (bool) ($validated['dry_run'] ?? false);
        $nombre = $builder->nombre($data['programa'], $validated['modo']);
        $meta = [
            'area' => 'contabilidad',
            'tipo' => 'cobranza',
            'modo' => $validated['modo'],
            'programa_id' => $data['programa']->id,
            'programa' => $data['programa']->nombre,
            'gestion' => $data['programa']->gestion ?? null,
        ];

        try {
            $result = $client->sendBatch($nombre, $data['items'], 'cobranza', null, $dryRun, null, $account, $meta);
        } catch (\Throwable $e) {
            return $this->errorEsam($e);
        }

        if (! $dryRun) {
            $this->registrarMensajes($data['logRows'], 'whatsapp_cobranza', $result['batch_id'] ?? null, $nombre);
        }

        return response()->json([
            'success' => true,
            'batch_id' => $result['batch_id'] ?? null,
            'total' => count($data['items']),
            'sin_telefono' => $data['sin_telefono'],
            'dry_run' => $dryRun,
        ]);
    }
    /** Avisos de notas de un módulo. */
    public function enviarAcademico(Request $request, $moduloId): JsonResponse
    {
        $tipoFiltro = $request->input('tipo', 'all');

        if (! in_array($tipoFiltro, ['all', 'aprobado', 'reprobado'], true)) {
            $tipoFiltro = 'all';
        }

        $modulo = DB::table('modulos')->where('id', $moduloId)->first();

        if (! $modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        $programa = DB::table('programas')->where('id', $modulo->programa_id)->first();
        $programaNombre = $programa->nombre ?? '';

        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        [$account, $accountError] = SenderAccount::resolve();

        if ($accountError) {
            return response()->json(['success' => false, 'message' => $accountError], 403);
        }

        $tieneNivelacion = DB::table('modulos_participantes')
            ->where('modulo_id', $moduloId)
            ->where(function ($q) {
                $q->whereNotNull('nota_nivelacion')->where('nota_nivelacion', '>', 0);
            })
            ->exists();

        // Selección del modal (participantes marcados). Sin selección se envía a
        // todos los que tienen nota, excepto los dados de baja.
        $seleccion = collect((array) $request->input('participante_ids', []))
            ->map(fn ($id) => (int) $id)->filter()->unique()->values();
        $conSeleccion = $request->has('participante_ids');

        $participantes = DB::table('modulos_participantes')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->leftJoin('inscripciones', function ($j) use ($modulo) {
                $j->on('inscripciones.participante_id', '=', 'participantes.id')
                    ->where('inscripciones.programa_id', '=', $modulo->programa_id);
            })
            ->where('modulos_participantes.modulo_id', $moduloId)
            ->when($conSeleccion, fn ($q) => $q->whereIn('participantes.id', $seleccion->all() ?: [0]))
            ->select(
                'modulos_participantes.id as mp_id',
                'participantes.id as participante_id',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.celular',
                'modulos_participantes.nota_modulo',
                'modulos_participantes.nota_nivelacion',
                'inscripciones.estado',
                'inscripciones.baja_operativa',
                'inscripciones.baja_academica',
                'inscripciones.baja_contable'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->unique('mp_id');

        $items = [];
        $logRows = [];
        $sinTelefono = 0;

        foreach ($participantes as $p) {
            if (! $conSeleccion && \App\Support\BajaInscripcion::es($p)) {
                continue;
            }

            $nm = $p->nota_modulo !== null ? (float) $p->nota_modulo : null;

            if ($nm === null || $nm <= 0) {
                continue;
            }

            $chatId = $this->chatId($p->celular);

            if (! $chatId) {
                $sinTelefono++;

                continue;
            }

            $nn = $p->nota_nivelacion !== null ? (float) $p->nota_nivelacion : null;
            $estado = 'Aprobado';
            $nivelacionTxt = '';

            if ($tieneNivelacion && $nm < 71) {
                if ($nn !== null && $nn >= 71) {
                    $estado = 'Aprobado';
                    $nivelacionTxt = 'Nivelación: '.number_format($nn, 2).' (Aprobado).';
                } else {
                    $estado = 'Reprobado';
                    $nivelacionTxt = 'Nivelación: '.($nn !== null ? number_format($nn, 2) : 'sin nota').'. Debe solicitar la 2da instancia y realizar el pago extra.';
                }
            } elseif ($nm < 71) {
                $estado = 'Reprobado';
            }

            if ($tipoFiltro === 'aprobado' && $estado !== 'Aprobado') {
                continue;
            }

            if ($tipoFiltro === 'reprobado' && $estado !== 'Reprobado') {
                continue;
            }

            $items[] = [
                'chat_id' => $chatId,
                'contact_name' => $p->apellidos.', '.$p->nombres,
                'external_ref' => 'notas:'.$p->mp_id.':'.now()->toDateString(),
                'variables' => [
                    'nombre_completo' => $p->apellidos.', '.$p->nombres,
                    'programa' => $programaNombre,
                    'modulo' => $modulo->nombre,
                    'nota' => number_format($nm, 2),
                    'estado' => $estado,
                    'nivelacion' => $nivelacionTxt,
                ],
            ];

            $logRows[] = [
                'participante_id' => $p->participante_id,
                'persona' => $p->apellidos.', '.$p->nombres,
            ];
        }

        if ($items === []) {
            return response()->json(['success' => false, 'message' => $conSeleccion
                ? 'Ninguno de los participantes seleccionados tiene nota para notificar en ese grupo.'
                : 'No hay participantes con nota para notificar.'], 404);
        }

        $dryRun = (bool) $request->boolean('dry_run');
        $nombre = 'Notas — '.$modulo->nombre.' — '.$programaNombre;
        $meta = [
            'area' => 'academico',
            'tipo' => 'notas',
            'programa_id' => $modulo->programa_id,
            'programa' => $programaNombre,
            'modulo' => $modulo->nombre,
        ];

        try {
            $result = $client->sendBatch($nombre, $items, 'academico', null, $dryRun, null, $account, $meta);
        } catch (\Throwable $e) {
            return $this->errorEsam($e);
        }

        if (! $dryRun) {
            $inscMap = DB::table('inscripciones')->where('programa_id', $modulo->programa_id)->pluck('id', 'participante_id');
            $logRows = array_map(fn ($r) => [
                'inscripcione_id' => (int) $inscMap->get($r['participante_id'], 0),
                'persona' => $r['persona'],
            ], $logRows);

            $this->registrarMensajes($logRows, 'whatsapp_academico', $result['batch_id'] ?? null, $nombre);
        }

        return response()->json([
            'success' => true,
            'batch_id' => $result['batch_id'] ?? null,
            'total' => count($items),
            'sin_telefono' => $sinTelefono,
            'dry_run' => $dryRun,
        ]);
    }

    /**
     * Aviso de inasistencia a los ausentes de una sesión, por Seguimiento ESAM
     * (desde la línea de quien envía, con anti-baneo). Antes se abría wa.me en
     * el teléfono del usuario y el envío no quedaba registrado en ningún lado:
     * ahora queda en el historial de la persona (tipo «asistencia»).
     */
    public function enviarAusentes(Request $request, $horarioId): JsonResponse
    {
        $validated = $request->validate([
            'participante_ids' => 'required|array|min:1|max:500',
            'participante_ids.*' => 'integer',
            'dry_run' => 'nullable|boolean',
        ]);

        $horario = DB::table('horarios')->where('id', $horarioId)->first();
        $modulo = $horario ? DB::table('modulos')->where('id', $horario->modulo_id)->first() : null;
        $programa = $modulo ? DB::table('programas')->where('id', $modulo->programa_id)->first() : null;

        if (! $horario || ! $modulo || ! $programa) {
            return response()->json(['success' => false, 'message' => 'Sesión no encontrada.'], 404);
        }

        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        [$account, $accountError] = SenderAccount::resolve();

        if ($accountError) {
            return response()->json(['success' => false, 'message' => $accountError], 403);
        }

        // Solo los que figuran como «Ausente» en esa sesión (y no están dados de baja).
        $ausentes = DB::table('asistencias as a')
            ->join('participantes as p', 'p.id', '=', 'a.participante_id')
            ->leftJoin('inscripciones as i', function ($j) use ($programa) {
                $j->on('i.participante_id', '=', 'p.id')->where('i.programa_id', '=', $programa->id);
            })
            ->where('a.horario_id', $horario->id)
            ->where('a.asistio', 'Ausente')
            ->whereIn('p.id', $validated['participante_ids'])
            ->select('p.id', 'p.nombres', 'p.apellidos', 'p.celular', 'i.id as inscripcion_id',
                'i.estado', 'i.baja_operativa', 'i.baja_academica', 'i.baja_contable')
            ->get()
            ->unique('id');

        $fecha = $horario->fecha_desarrollo ? Carbon::parse($horario->fecha_desarrollo)->format('d/m/Y') : '';
        $hora = trim(substr((string) $horario->hora_inicio, 0, 5).($horario->hora_fin ? ' - '.substr((string) $horario->hora_fin, 0, 5) : ''));
        $sesion = trim($fecha.($hora !== '' ? ' · '.$hora : ''));
        $enviaNombre = auth()->user()->fullname ?? auth()->user()->username ?? 'Área Académica';

        $items = [];
        $logRows = [];
        $sinTelefono = 0;
        $bajas = 0;

        foreach ($ausentes as $p) {
            if (\App\Support\BajaInscripcion::es($p)) {
                $bajas++;

                continue;
            }

            $chatId = $this->chatId($p->celular);

            if (! $chatId) {
                $sinTelefono++;

                continue;
            }

            $nombre = $p->apellidos.', '.$p->nombres;
            $items[] = [
                'chat_id' => $chatId,
                'contact_name' => $nombre,
                'external_ref' => 'asistencia:'.$horario->id.':'.$p->id,
                'body_override' => 'Estimado/a '.$nombre.', registró inasistencia en la sesión del '.$sesion
                    .' del programa '.$programa->nombre.', módulo '.$modulo->nombre.'.'
                    ."\n\nPor favor, comuníquese con su responsable para justificar su falta."
                    ."\n\nÁrea Académica ESAM Sucre Central\nEnviado por: ".$enviaNombre,
            ];
            $logRows[] = ['inscripcione_id' => (int) ($p->inscripcion_id ?? 0), 'persona' => $nombre];
        }

        if ($items === []) {
            return response()->json([
                'success' => false,
                'message' => $sinTelefono > 0
                    ? 'Ninguno de los ausentes seleccionados tiene un celular válido.'
                    : ($bajas > 0
                        ? 'Los ausentes seleccionados están dados de baja: no se les envía el aviso.'
                        : 'Ninguno de los seleccionados figura como ausente en esta sesión.'),
            ], 422);
        }

        $dryRun = (bool) ($validated['dry_run'] ?? false);
        $nombreLote = 'Inasistencia — '.$modulo->nombre.' — '.$sesion;
        $meta = [
            'area' => 'academico',
            'tipo' => 'asistencia',
            'programa_id' => $programa->id,
            'programa' => $programa->nombre,
            'gestion' => $programa->gestion ?? null,
            'modulo' => $modulo->nombre,
            'sesion' => $sesion,
        ];

        try {
            $result = $client->sendBatch($nombreLote, $items, 'academico', null, $dryRun, null, $account, $meta);
        } catch (\Throwable $e) {
            return $this->errorEsam($e);
        }

        if (! $dryRun) {
            $this->registrarMensajes($logRows, 'whatsapp_asistencia', $result['batch_id'] ?? null, $nombreLote);
        }

        return response()->json([
            'success' => true,
            'batch_id' => $result['batch_id'] ?? null,
            'total' => count($items),
            'sin_telefono' => $sinTelefono,
            'dry_run' => $dryRun,
        ]);
    }

    private function registrarMensajes(array $rows, string $tipo, ?int $batchId, string $nombre): void
    {
        if ($rows === []) {
            return;
        }

        $now = now();
        $insert = [];

        foreach ($rows as $row) {
            $insert[] = [
                'mensaje' => $nombre.' — lote #'.$batchId,
                'inscripcione_id' => $row['inscripcione_id'] ?? 0,
                'fecha_registro' => $now->toDateString(),
                'tipo' => $tipo,
                'persona' => $row['persona'] ?? null,
            ];
        }

        DB::table('mensajes')->insert($insert);
    }

    private function chatId(?string $celular): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $celular);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 8) {
            $digits = '591'.$digits;
        }

        if (strlen($digits) < 8) {
            return null;
        }

        return $digits.'@c.us';
    }

    /**
     * Mapa [cuota_id => número] por tipo, sobre TODAS las cuotas del participante
     * (Matrícula = 1; Mensualidad = 1..N por fecha).
     */
    private function numerosPorTipo($cuotas): array
    {
        $numeros = [];
        $contadores = ['Matricula' => 0, 'Colegiatura' => 0];

        $ordenadas = $cuotas->sortBy(fn ($c) => ($c->tipo === 'Matricula' ? '0' : '1').'-'.$c->fecha_pago);

        foreach ($ordenadas as $c) {
            $key = $c->tipo === 'Matricula' ? 'Matricula' : 'Colegiatura';
            $contadores[$key]++;
            $numeros[$c->id] = $contadores[$key];
        }

        return $numeros;
    }
}
