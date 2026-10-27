<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeguimientoEsam\SeguimientoEsamSso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Redirige al usuario de cronogramas a Seguimiento ESAM con SSO firmado.
 */
class SeguimientoEsamSsoController extends Controller
{
    /** `?next=/notes` abre directamente esa vista del panel (solo rutas internas). */
    public function redirect(Request $request, SeguimientoEsamSso $sso): RedirectResponse
    {
        $next = (string) $request->query('next', '');
        $next = preg_match('#^/[A-Za-z0-9/_\-?=&.]*$#', $next) && ! str_starts_with($next, '//') ? $next : null;

        return redirect()->away($sso->urlFor(auth()->user(), $next));
    }
}
