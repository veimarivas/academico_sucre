<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Acceso al apartado "WhatsApp Seguimiento ESAM": Super Admin o usuarios
 * marcados como autorizados para usar Seguimiento ESAM.
 */
class EnsureWhatsappAuthorized
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user, 401);

        if ($user->hasRole('Super Admin') || $user->whatsapp_autorizado) {
            return $next($request);
        }

        abort(403, 'No estás autorizado para usar Seguimiento ESAM.');
    }
}
