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
    public function sendBatch(string $name, array $items, ?string $category = null, ?int $templateId = null, bool $dryRun = false, ?array $defaultMedia = null, ?string $account = null, ?array $meta = null): array
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

        if ($meta) {
            $payload['meta'] = $meta;
        }

        return $this->unwrap($this->request()->post('/api/v1/batches', $payload));
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

            throw new RuntimeException("Seguimiento ESAM ({$response->status()}): {$message}");
        }

        return $response->json() ?? [];
    }
}
