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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
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

        $participantes = DB::table('modulos_participantes')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->where('modulos_participantes.modulo_id', $moduloId)
            ->select(
                'modulos_participantes.id as mp_id',
                'participantes.id as participante_id',
                'participantes.nombres',
                'participantes.apellidos',
                'participantes.celular',
                'modulos_participantes.nota_modulo',
                'modulos_participantes.nota_nivelacion'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get();

        $items = [];
        $logRows = [];
        $sinTelefono = 0;

        foreach ($participantes as $p) {
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
            return response()->json(['success' => false, 'message' => 'No hay participantes con nota para notificar.'], 404);
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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
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
