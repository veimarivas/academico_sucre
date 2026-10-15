<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Services\SeguimientoEsam\SeguimientoEsamClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Ajustes → WhatsApp: registra el número autorizado y muestra el estado de
 * Seguimiento ESAM.
 */
class AjustesWhatsappController extends Controller
{
    public function index(): View
    {
        $numero = Configuracion::get('whatsapp_numero');
        $diasEnvio = Configuracion::get('whatsapp_dias_envio', '1,5,10,15');
        $client = app(SeguimientoEsamClient::class);
        $health = null;
        $error = null;

        // Datos del usuario que ingresó: su número registrado y si está autorizado.
        $authUser = auth()->user();
        $numeroUsuario = $authUser?->whatsapp_numero;
        $usuarioAutorizado = (bool) ($authUser?->whatsapp_autorizado);
        $usuarioArea = $authUser?->whatsapp_area;

        if ($client->enabled()) {
            try {
                // Estado de la sesión del usuario que ingresó (su número), no la global.
                $authUser = auth()->user();
                $uid = $authUser ? (string) $authUser->id : null;
                $phone = $authUser->whatsapp_numero ?? null;
                $health = $client->health(null, $uid, $phone);
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = 'La integración con Seguimiento ESAM no está configurada (faltan URL o token).';
        }

        // Recordatorios automáticos de cobranza: apagados por defecto, una sola cuenta de envío.
        $recAuto = Configuracion::get('whatsapp_recordatorios_auto', '0') === '1';
        $recCuenta = (int) Configuracion::get('whatsapp_recordatorios_cuenta', '0');
        $recHora = Configuracion::get('whatsapp_recordatorios_hora', '09:00');
        $cuentasEnvio = \App\Models\User::query()
            ->where('whatsapp_autorizado', true)
            ->whereNotNull('whatsapp_numero')
            ->where('whatsapp_numero', '!=', '')
            ->orderBy('fullname')
            ->get(['id', 'fullname', 'whatsapp_numero']);
        $puedeAutomatizar = (bool) $authUser?->hasRole('Super Admin');

        return view('admin.ajustes.whatsapp', compact(
            'recAuto',
            'recCuenta',
            'recHora',
            'cuentasEnvio',
            'puedeAutomatizar',
            'numero',
            'diasEnvio',
            'health',
            'error',
            'numeroUsuario',
            'usuarioAutorizado',
            'usuarioArea',
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        // Formulario de recordatorios automáticos: solo toca sus ajustes (no el número global).
        if ($request->has('rec_form')) {
            return $this->guardarRecordatorios($request);
        }

        $validated = $request->validate([
            'numero' => 'nullable|string|max:20',
            'dias_envio' => 'nullable|string|max:120',
        ]);

        $digits = preg_replace('/\D+/', '', (string) ($validated['numero'] ?? ''));

        if ($digits !== '' && strlen($digits) === 8) {
            $digits = '591'.$digits;
        }

        Configuracion::set('whatsapp_numero', $digits ?: null);

        $dias = collect(explode(',', (string) ($validated['dias_envio'] ?? '')))
            ->map(fn ($d) => (int) trim($d))
            ->filter(fn ($d) => $d >= 1 && $d <= 31)
            ->unique()
            ->sort()
            ->values()
            ->implode(',');

        Configuracion::set('whatsapp_dias_envio', $dias ?: '1,5,10,15');

        $client = app(SeguimientoEsamClient::class);

        if ($client->enabled()) {
            try {
                $client->setNumero($digits);
            } catch (\Throwable $e) {
                return back()->with('error', 'Se guardó localmente, pero Seguimiento ESAM rechazó el número: '.$e->getMessage());
            }
        }

        return back()->with('success', 'Número autorizado guardado.');
    }

    /**
     * Recordatorios automáticos de cobranza (solo Super Admin): interruptor, días,
     * hora y la única cuenta desde cuya línea salen. Siempre requieren confirmación
     * en Seguimiento ESAM antes de enviarse.
     */
    private function guardarRecordatorios(Request $request): RedirectResponse
    {
        if (! $request->user()?->hasRole('Super Admin')) {
            return back()->with('error', 'Solo el Super Admin puede configurar los recordatorios automáticos.');
        }

        $rec = $request->validate([
            'rec_auto' => 'nullable|boolean',
            'rec_cuenta' => 'nullable|integer',
            'rec_hora' => ['nullable', 'regex:/^\d{2}:\d{2}$/'],
            'dias_envio' => 'nullable|string|max:120',
        ]);

        $auto = (bool) ($rec['rec_auto'] ?? false);
        $cuenta = (int) ($rec['rec_cuenta'] ?? 0);
        $valida = $cuenta && \App\Models\User::where('id', $cuenta)
            ->where('whatsapp_autorizado', true)
            ->whereNotNull('whatsapp_numero')
            ->where('whatsapp_numero', '!=', '')
            ->exists();

        if ($auto && ! $valida) {
            return back()->with('error', 'Para activar los recordatorios automáticos elegí una cuenta con número de WhatsApp autorizado.');
        }

        $dias = collect(explode(',', (string) ($rec['dias_envio'] ?? '')))
            ->map(fn ($d) => (int) trim($d))
            ->filter(fn ($d) => $d >= 1 && $d <= 31)
            ->unique()
            ->sort()
            ->values()
            ->implode(',');

        Configuracion::set('whatsapp_recordatorios_auto', $auto ? '1' : '0');
        Configuracion::set('whatsapp_recordatorios_cuenta', $valida ? (string) $cuenta : null);
        Configuracion::set('whatsapp_recordatorios_hora', $rec['rec_hora'] ?? '09:00');
        Configuracion::set('whatsapp_dias_envio', $dias ?: '1,5,10,15');

        return back()->with('success', $auto
            ? 'Recordatorios automáticos activados. Cada envío quedará esperando tu confirmación en Seguimiento ESAM.'
            : 'Recordatorios automáticos desactivados.');
    }

    /**
     * Estado resumido de Seguimiento ESAM para el indicador del topbar.
     *
     * Libera el bloqueo de sesión antes de llamar a la API y cachea el
     * resultado (incluidos los errores) para no serializar el resto de las
     * peticiones AJAX de la página ni golpear la API en cada navegación.
     */
    public function estado(SeguimientoEsamClient $client): JsonResponse
    {
        $request = request();

        if ($request->hasSession()) {
            $request->session()->save();
        }

        // La sesión de WhatsApp es por usuario: el estado se consulta y se cachea
        // por cuenta, para no mostrar el número de la sesión de otro usuario.
        $authUser = $request->user();
        $uid = $authUser ? (string) $authUser->id : 'anon';
        $phone = $authUser->whatsapp_numero ?? null;

        $payload = Cache::remember("esam_health_estado:{$uid}", now()->addSeconds(45), function () use ($client, $uid, $phone) {
            if (! $client->enabled()) {
                return ['enabled' => false, 'connected' => false, 'status' => 'disabled'];
            }

            try {
                $health = $client->health(3, $uid, $phone);
            } catch (\Throwable $e) {
                return ['enabled' => true, 'connected' => false, 'status' => 'error'];
            }

            $session = $health['session'] ?? [];

            return [
                'enabled' => true,
                'connected' => (bool) ($session['connected'] ?? false),
                'status' => $session['status'] ?? 'unknown',
                'phone' => $session['phone'] ?? null,
                'verified' => (bool) ($session['verified'] ?? false),
            ];
        });

        return response()->json($payload);
    }
}
