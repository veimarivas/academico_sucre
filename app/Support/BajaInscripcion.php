<?php

namespace App\Support;

/**
 * Estado de baja de una inscripción, con la misma regla que toggleBaja:
 * temprana (estado = 0), operativa, académica o contable.
 */
class BajaInscripcion
{
    /** ¿La inscripción está dada de baja? Acepta fila u objeto con esas columnas (o null = sin inscripción). */
    public static function es($ins): bool
    {
        if (! $ins) {
            return false;
        }

        return (isset($ins->estado) && (int) $ins->estado === 0)
            || (int) ($ins->baja_operativa ?? 0) === 1
            || (int) ($ins->baja_academica ?? 0) === 1
            || (int) ($ins->baja_contable ?? 0) === 1;
    }

    /** Etiqueta legible del tipo de baja, o null si está activa. */
    public static function motivo($ins): ?string
    {
        if (! self::es($ins)) {
            return null;
        }

        $tipos = [];

        if ((int) ($ins->baja_operativa ?? 0) === 1) {
            $tipos[] = 'operativa';
        }

        if ((int) ($ins->baja_academica ?? 0) === 1) {
            $tipos[] = 'académica';
        }

        if ((int) ($ins->baja_contable ?? 0) === 1) {
            $tipos[] = 'contable';
        }

        if ($tipos === [] && isset($ins->estado) && (int) $ins->estado === 0) {
            $tipos[] = 'temprana';
        }

        return 'Baja '.implode(' / ', $tipos);
    }

    /** Condición SQL "activa" sobre el alias de inscripciones (para filtrar en consultas). */
    public static function sqlActiva(string $alias = 'inscripciones'): string
    {
        return "({$alias}.estado = 1 AND COALESCE({$alias}.baja_operativa, 0) = 0"
            ." AND COALESCE({$alias}.baja_academica, 0) = 0 AND COALESCE({$alias}.baja_contable, 0) = 0)";
    }
}
