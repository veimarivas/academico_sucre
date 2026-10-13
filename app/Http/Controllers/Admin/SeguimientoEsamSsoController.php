<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SeguimientoEsam\SeguimientoEsamSso;
use Illuminate\Http\RedirectResponse;

/**
 * Redirige al usuario de cronogramas a Seguimiento ESAM con SSO firmado.
 */
class SeguimientoEsamSsoController extends Controller
{
    public function redirect(SeguimientoEsamSso $sso): RedirectResponse
    {
        return redirect()->away($sso->urlFor(auth()->user()));
    }
}
