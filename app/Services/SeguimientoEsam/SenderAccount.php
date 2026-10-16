<?php

namespace App\Services\SeguimientoEsam;

/**
 * Resuelve desde qué cuenta corporativa envía el usuario autenticado.
 *
 * Cada envío sale SOLO desde la línea de WhatsApp de quien lo hace:
 * - Usuario con número registrado y autorizado → su propia cuenta.
 * - Usuario con número pero sin autorización → no puede enviar (se explica).
 * - Usuario sin número → no puede enviar, aunque sea administrador.
 *
 * Antes, un administrador sin número (o con número pero sin autorizar) enviaba
 * por la línea principal, que es el número de otra persona.
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

        if (blank($user->whatsapp_numero)) {
            return [null, 'Tu usuario no tiene un número de WhatsApp registrado. Los envíos salen solo desde tu propia línea: pedile a un administrador que registre tu número en Usuarios.'];
        }

        if (! $user->whatsapp_autorizado) {
            return [null, 'Tu número de WhatsApp ('.$user->whatsapp_numero.') no está autorizado para enviar. Pedile a un administrador que lo habilite en Usuarios.'];
        }

        return [(string) $user->id, null];
    }
}
