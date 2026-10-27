<?php

namespace App\Services\SeguimientoEsam;

use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Cliente de la API de Seguimiento ESAM (/api/v1).
 *
 * Autentica con Bearer token y crea lotes de envío por WhatsApp.
 */
class SeguimientoEsamClient
{
    public function enabled(): bool
    {
        return (bool) config('services.seguimiento_esam.enabled')
            && (string) config('services.seguimiento_esam.token') !== '';
    }

    private function request()
    {
        if (! $this->enabled()) {
            throw new RuntimeException('La integración con Seguimiento ESAM no está configurada.');
        }

        return Http::baseUrl(rtrim((string) config('services.seguimiento_esam.url'), '/'))
            ->withToken((string) config('services.seguimiento_esam.token'))
            ->acceptJson()
            ->timeout((int) config('services.seguimiento_esam.timeout', 15));
    }

    /**
     * Estado de salud del gateway. Con `$uid`/`$phone` devuelve la sesión de ese
     * usuario (una por trabajador) en vez de la sesión principal global.
     */
    public function health(?int $timeout = null, ?string $uid = null, ?string $phone = null): array
    {
        $request = $this->request();

        if ($timeout !== null) {
            $request = $request->timeout($timeout);
        }

        $query = array_filter([
            'uid' => $uid,
            'phone' => $phone,
        ], fn ($v) => $v !== null && $v !== '');

        return $this->unwrap($request->get('/api/v1/health'.($query ? '?'.http_build_query($query) : '')));
    }

    /**
     * Crea un lote de envío.
     *
     * @param  array<int, array{chat_id: string, contact_name?: string, variables?: array, body_override?: string, external_ref?: string}>  $items
     */
    /**
     * @param  array{require_confirmation?: bool, confirm_group?: string, scheduled_at?: string, confirm_description?: string, notify?: bool}  $options
     *         Programación con confirmación: el lote queda «esperando confirmación» en
     *         Seguimiento ESAM y no se envía hasta que el dueño de la línea lo confirme.
     */
    public function sendBatch(string $name, array $items, ?string $category = null, ?int $templateId = null, bool $dryRun = false, ?array $defaultMedia = null, ?string $account = null, ?array $meta = null, array $options = []): array
    {
        $payload = [
            'name' => mb_substr($name, 0, 120),
            'category' => $category,
            'template_id' => $templateId,
            'items' => $items,
            'dry_run' => $dryRun,
        ];

        if ($defaultMedia) {
            $payload['default_media'] = $defaultMedia;
        }

        if ($account) {
            $payload['account'] = $account;
        }

        // Quién pidió el envío (se ve en el detalle del lote en Seguimiento ESAM).
        $solicita = auth()->user();
        $meta = array_merge($meta ?? [], array_filter([
            'solicitado_por' => $solicita?->fullname ?? $solicita?->username,
            'solicitado_por_id' => $solicita?->id,
            'origen_sistema' => 'cronogramas',
        ], fn ($v) => $v !== null && $v !== ''));

        if ($meta) {
            $payload['meta'] = $meta;
        }

        // Reenvío intencional: el usuario confirmó repetir lo ya enviado hoy.
        if (request()?->boolean('reenviar')) {
            $payload['allow_resend'] = true;
        }

        foreach (['require_confirmation', 'confirm_group', 'scheduled_at', 'confirm_description', 'notify'] as $key) {
            if (array_key_exists($key, $options)) {
                $payload[$key] = $options[$key];
            }
        }

        return $this->unwrap($this->request()->post('/api/v1/batches', $payload));
    }

    /** Avisa (una sola vez) al dueño de la línea que hay una programación para confirmar. */
    public function notifyConfirmGroup(string $group, ?string $description = null): array
    {
        return $this->unwrap($this->request()->post('/api/v1/batches/confirm-group/'.rawurlencode($group).'/notify', [
            'description' => $description,
        ]));
    }

    /** Grupos de WhatsApp disponibles en la sesión. */
    public function groups(): array
    {
        return $this->unwrap($this->request()->get('/api/v1/groups'));
    }

    /** Resuelve un enlace/código de invitación al grupo (id, nombre). */
    public function resolveGroup(string $invite): array
    {
        return $this->unwrap($this->request()->post('/api/v1/groups/resolve', ['invite' => $invite]));
    }

    /** Registra/actualiza la cuenta corporativa (sesión por trabajador). */
    public function registerAccount(User $user, ?string $password = null): array
    {
        $payload = [
            'uid' => (string) $user->id,
            'name' => $user->fullname ?? $user->username,
            'username' => $user->username,
            'email' => $user->email,
            'area' => $user->whatsapp_area,
            'phone' => $user->whatsapp_numero,
            'roles' => $user->getRoleNames()->values()->all(),
            'authorized' => (bool) $user->whatsapp_autorizado,
        ];

        // Solo se envía cuando cronogramas define/cambia la contraseña; así el
        // acceso directo a Seguimiento ESAM queda sincronizado.
        if ($password !== null && $password !== '') {
            $payload['password'] = $password;
        }

        return $this->unwrap($this->request()->post('/api/v1/accounts', $payload));
    }

    /**
     * Envía la nueva contraseña a Seguimiento ESAM para que el acceso directo
     * al panel siga funcionando. No interrumpe el cambio en cronogramas: si la
     * integración está apagada o falla, solo queda registrado en el log.
     */
    public function syncPassword(User $user, string $password): void
    {
        if (! $this->enabled() || $password === '') {
            return;
        }

        try {
            $this->registerAccount($user->fresh() ?? $user, $password);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('No se pudo sincronizar la contraseña con Seguimiento ESAM', [
                'user' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Prospecto de marketing → nota de seguimiento del asesor en Seguimiento ESAM
     * (área Marketing de /notes). Idempotente por referencia: crea o actualiza.
     *
     * @param  array{account: string, phone: string, name?: ?string, programa?: ?string, programa_id?: ?int, estado?: ?string, nota?: ?string}  $data
     */
    public function upsertProspect(string $ref, array $data): array
    {
        return $this->unwrap($this->request()->put('/api/v1/prospects/'.rawurlencode($ref), $data));
    }

    /** El prospecto se eliminó: su seguimiento pasa a la papelera de Notas. */
    public function deleteProspect(string $ref): array
    {
        return $this->unwrap($this->request()->delete('/api/v1/prospects/'.rawurlencode($ref)));
    }

    public function batchStatus(int $batchId): array
    {
        return $this->unwrap($this->request()->get("/api/v1/batches/{$batchId}"));
    }

    public function setNumero(string $numero): array
    {
        return $this->unwrap($this->request()->put('/api/v1/config/numero', ['numero' => $numero]));
    }

    public function numero(): array
    {
        return $this->unwrap($this->request()->get('/api/v1/config/numero'));
    }

    private function unwrap(Response $response): array
    {
        if ($response->failed()) {
            $message = $response->json('message') ?? $response->body();
            $message = is_array($message) ? implode('; ', $message) : $message;

            // 409/422 traen un mensaje para el usuario: se muestra tal cual.
            $texto = in_array($response->status(), [409, 422], true) ? (string) $message : "Seguimiento ESAM ({$response->status()}): {$message}";

            throw new SeguimientoEsamException($texto, $response->status(), (array) ($response->json() ?? []));
        }

        return $response->json() ?? [];
    }
}
