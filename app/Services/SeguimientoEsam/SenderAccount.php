<?php

namespace App\Services\SeguimientoEsam;

/**
 * Resuelve desde qué cuenta corporativa envía el usuario autenticado.
 *
 * - Trabajador autorizado con número corporativo → envía desde su cuenta.
 * - Usuario con permiso `whatsapp.admin` sin número → sesión principal (gestión).
 * - Cualquier otro → no autorizado.
 */
class SenderAccount
{
    /**
     * @return array{0: ?string, 1: ?string} [account, error]
     */
    public static function resolve(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [null, 'No autenticado.'];
        }

        if ($user->whatsapp_autorizado && $user->whatsapp_numero) {
            return [(string) $user->id, null];
        }

        if ($user->can('whatsapp.admin')) {
            return [null, null];
        }

        return [null, 'Tu usuario no está autorizado para enviar por WhatsApp. Pedile a un administrador que lo habilite.'];
    }
}
