<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use Illuminate\Http\JsonResponse;

/**
 * Consulta el progreso de un lote de envío en Seguimiento ESAM.
 */
class WhatsappLoteController extends Controller
{
    public function show(int $batch): JsonResponse
    {
        $client = app(SeguimientoEsamClient::class);

        if (! $client->enabled()) {
            return response()->json(['success' => false, 'message' => 'La integración con Seguimiento ESAM no está configurada.'], 422);
        }

        try {
            $data = $client->batchStatus($batch);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 502);
        }

        // Lo ve quien tiene permisos de envío o quien pidió ese lote (p. ej. un
        // responsable de programa que avisó a los ausentes de una sesión).
        $user = auth()->user();
        $puede = $user->can('whatsapp.admin') || $user->can('contable.cobrar') || $user->can('programas.editar')
            || $user->can('programas.modulos.estado') || (string) ($data['requested_by'] ?? '') === (string) $user->id;
        abort_unless($puede, 403, 'No tenés permiso para ver este lote.');

        return response()->json(['success' => true] + $data);
    }
}
