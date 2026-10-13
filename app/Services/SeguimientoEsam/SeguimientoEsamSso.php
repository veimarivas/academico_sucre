<?php

namespace App\Services\SeguimientoEsam;

use App\Models\User;
use Illuminate\Support\Str;

/**
 * Genera la URL de SSO firmada (HMAC-SHA256) hacia Seguimiento ESAM.
 */
class SeguimientoEsamSso
{
    public function enabled(): bool
    {
        return (string) config('services.seguimiento_esam.sso_secret') !== ''
            && (string) config('services.seguimiento_esam.panel_url') !== '';
    }

    public function urlFor(?User $user = null, ?string $next = null): string
    {
        $user ??= auth()->user();
        // El SSO vive en el panel, no en la API de integración.
        $base = rtrim((string) config('services.seguimiento_esam.panel_url'), '/');

        if (! $this->enabled() || ! $user) {
            return $base;
        }

        $payload = [
            'uid' => (string) $user->id,
            'name' => $user->fullname ?? $user->name ?? '',
            'email' => $user->email,
            'username' => $user->username,
            'autorizado' => (bool) $user->whatsapp_autorizado,
            'area' => $user->whatsapp_area,
            'numero' => $user->whatsapp_numero,
            'roles' => $user->getRoleNames()->values()->all(),
            'exp' => time() + 60,
            'nonce' => Str::random(32),
            'next' => $next,
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $signature = hash_hmac('sha256', $json, (string) config('services.seguimiento_esam.sso_secret'));
        $token = $this->base64UrlEncode($json).'.'.$signature;

        return $base.'/sso?token='.urlencode($token);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
