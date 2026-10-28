<?php

namespace App\Support;

use App\Models\User;

/**
 * Firma de los mensajes masivos: «Nombre completo - Nombre del área».
 * Seguimiento ESAM la agrega al final de cada mensaje como «Atte: …».
 *
 * El área es la del usuario que envía: su área de WhatsApp o, si no, la de su
 * rol. Solo quien no tiene área propia (p. ej. el Super Admin) firma con el área
 * del envío (cobranza → Contable, notas/asistencia → Académica, prospectos →
 * Marketing): un Super Admin que envía cobranzas firma «… - Área Contable».
 */
class FirmaWhatsapp
{
    private const AREAS = [
        'contabilidad' => 'Área Contable',
        'academico' => 'Área Académica',
        'marketing' => 'Área de Marketing',
    ];

    private const ROLES = [
        'Contabilidad' => 'contabilidad',
        'Academicos' => 'academico',
        'Responsable Académico' => 'academico',
        'Responsable Académico UPI' => 'academico',
        'Gerencia Marketing' => 'marketing',
        'Supervisor de Marketing' => 'marketing',
        'Asesor de Marketing' => 'marketing',
    ];

    public static function nombre(?User $user): string
    {
        return MensajeNotas::limpio((string) ($user?->fullname ?: $user?->username ?: ''));
    }

    /** Nombre del área para la firma (o null si no se puede saber). */
    public static function area(?User $user, ?string $areaEnvio = null): ?string
    {
        // 1) Área configurada del usuario.
        $clave = $user?->whatsapp_area;
        // 2) Su rol (el área a la que pertenece).
        if (! isset(self::AREAS[(string) $clave]) && $user) {
            foreach (self::ROLES as $rol => $area) {
                if ($user->hasRole($rol)) {
                    $clave = $area;
                    break;
                }
            }
        }
        // 3) Sin área propia (p. ej. Super Admin): el área del envío.
        if (! isset(self::AREAS[(string) $clave])) {
            $clave = $areaEnvio;
        }

        return self::AREAS[(string) $clave] ?? null;
    }

    /** «Nombre completo - Nombre del área» (o solo el nombre). */
    public static function para(?User $user, ?string $areaEnvio = null): string
    {
        $nombre = self::nombre($user);
        if ($nombre === '') {
            return '';
        }
        $area = self::area($user, $areaEnvio);

        return $area ? "{$nombre} - {$area}" : $nombre;
    }
}
