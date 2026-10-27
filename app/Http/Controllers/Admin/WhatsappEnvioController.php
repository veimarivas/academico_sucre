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

    /**
     * Notas de un módulo por WhatsApp (Seguimiento ESAM). `modo`:
     * - `regular`: nota del módulo (si reprobó, debe realizar la nivelación).
     * - `nivelacion`: resultado de la nivelación (si vuelve a reprobar, la 2da
     *   instancia tiene costo: responsable del programa y área contable).
     * El texto es el de la vista previa (App\Support\MensajeNotas).
     */
    public function enviarAcademico(Request $request, $moduloId): JsonResponse
    {
        $tipoFiltro = $request->input('tipo', 'all');
        if (! in_array($tipoFiltro, ['all', 'aprobado', 'reprobado'], true)) {
            $tipoFiltro = 'all';
        }
        $modo = $request->input('modo') === 'nivelacion' ? 'nivelacion' : 'regular';

        $data = \App\Support\MensajeNotas::construir((int) $moduloId, $modo);
        $modulo = $data['modulo'];

        if (! $modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado'], 404);
        }

        if ($modo === 'nivelacion' && ! $data['tiene_nivelacion']) {
            return response()->json(['success' => false, 'message' => 'Todavía no se registraron notas de nivelación en este módulo.'], 422);
        }

        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        [$account, $accountError] = SenderAccount::resolve();

        if ($accountError) {
            return response()->json(['success' => false, 'message' => $accountError], 403);
        }

        // Selección del modal (participantes marcados). Sin selección se envía a
        // todos los que tienen mensaje, excepto los dados de baja.
        $seleccion = collect((array) $request->input('participante_ids', []))
            ->map(fn ($id) => (int) $id)->filter()->unique()->values();
        $conSeleccion = $request->has('participante_ids');
        $programaNombre = $data['programa']->nombre ?? '';
        $hoy = now()->toDateString();

        $items = [];
        $logRows = [];
        $sinTelefono = 0;

        foreach ($data['mensajes'] as $m) {
            if (! $m['texto']) {
                continue;
            }
            if ($conSeleccion ? ! $seleccion->contains((int) $m['participante_id']) : (bool) $m['baja']) {
                continue;
            }
            if ($tipoFiltro !== 'all' && $m['tipo'] !== $tipoFiltro) {
                continue;
            }

            $chatId = $this->chatId($m['celular']);

            if (! $chatId) {
                $sinTelefono++;

                continue;
            }

            $nombre = $m['apellidos'].', '.$m['nombres'];
            $items[] = [
                'chat_id' => $chatId,
                'contact_name' => $nombre,
                'external_ref' => 'notas:'.$modo.':'.$m['mp_id'].':'.$hoy,
                'body_override' => $m['texto'],
            ];
            $logRows[] = ['participante_id' => $m['participante_id'], 'persona' => $nombre];
        }

        if ($items === []) {
            return response()->json(['success' => false, 'message' => $conSeleccion
                ? 'Ninguno de los participantes seleccionados tiene un mensaje para enviar.'
                : 'No hay participantes con nota para notificar.'], 404);
        }

        $dryRun = (bool) $request->boolean('dry_run');
        $nombreLote = ($modo === 'nivelacion' ? 'Nivelación — ' : 'Notas — ').$modulo->nombre.' — '.$programaNombre;
        $meta = [
            'area' => 'academico',
            'tipo' => 'notas',
            'modo' => $modo,
            'programa_id' => $modulo->programa_id,
            'programa' => $programaNombre,
            'modulo' => $modulo->nombre,
        ];

        try {
            $result = $client->sendBatch($nombreLote, $items, 'academico', null, $dryRun, null, $account, $meta);
        } catch (\Throwable $e) {
            return $this->errorEsam($e);
        }

        if (! $dryRun) {
            $inscMap = DB::table('inscripciones')->where('programa_id', $modulo->programa_id)->pluck('id', 'participante_id');
            $logRows = array_map(fn ($r) => [
                'inscripcione_id' => (int) $inscMap->get($r['participante_id'], 0),
                'persona' => $r['persona'],
            ], $logRows);

            $this->registrarMensajes($logRows, 'whatsapp_academico', $result['batch_id'] ?? null, $nombreLote);
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
                    ."\n\nÁrea Académica ESAM Sucre Central",
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
