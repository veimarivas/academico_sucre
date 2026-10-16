<?php

namespace App\Http\Controllers;

use App\Services\SeguimientoEsam\SeguimientoEsamException;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Respuesta JSON para un error al enviar por Seguimiento ESAM: 409 cuando
     * todo ya se había enviado hoy (la vista ofrece reenviar), 422 cuando no se
     * puede enviar (p. ej. línea no vinculada) y 502 si falla la conexión.
     */
    protected function errorEsam(\Throwable $e): JsonResponse
    {
        $status = $e instanceof SeguimientoEsamException && in_array($e->status, [409, 422], true) ? $e->status : 502;

        return response()->json([
            'success' => false,
            'duplicado' => $e instanceof SeguimientoEsamException && $e->isDuplicate(),
            'message' => $e->getMessage(),
        ], $status);
    }
}
