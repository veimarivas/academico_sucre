<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Mensajes de WhatsApp de las notas de un módulo, en un solo lugar: la vista
 * previa del modal y el envío por Seguimiento ESAM usan exactamente este texto.
 *
 * Modos:
 * - `regular`: nota del módulo. Si reprobó, debe realizar la nivelación y estar
 *   atento al comunicado de la actividad.
 * - `nivelacion`: solo para quienes reprobaron la nota regular. Si vuelve a
 *   reprobar (o no tiene nota de nivelación), la siguiente nivelación (2da
 *   instancia) tiene costo y debe comunicarse con el responsable del programa
 *   y con el área contable.
 *
 * La firma «Atte: nombre completo» la agrega Seguimiento ESAM al enviar.
 */
class MensajeNotas
{
    public const APROBACION = 71;

    public const MODOS = ['regular', 'nivelacion'];

    /**
     * @return array{modulo: ?object, programa: ?object, tiene_nivelacion: bool, mensajes: array<int, array<string, mixed>>}
     */
    public static function construir(int $moduloId, string $modo = 'regular'): array
    {
        $modo = in_array($modo, self::MODOS, true) ? $modo : 'regular';
        $modulo = DB::table('modulos')->where('id', $moduloId)->first();

        if (! $modulo) {
            return ['modulo' => null, 'programa' => null, 'tiene_nivelacion' => false, 'mensajes' => []];
        }

        $programa = DB::table('programas')->where('id', $modulo->programa_id)->first();
        // Nombres limpios: algunos módulos se guardaron con tabuladores o saltos
        // de línea, que rompen la negrita (*…*) en WhatsApp.
        $programaNombre = self::limpio($programa->nombre ?? '');
        $moduloNombre = self::limpio($modulo->nombre);

        // Todos los participantes del módulo, también los dados de baja (se
        // muestran sin seleccionar para que no reciban el envío masivo).
        $participantes = DB::table('modulos_participantes')
            ->join('participantes', 'modulos_participantes.participante_id', '=', 'participantes.id')
            ->leftJoin('inscripciones', function ($j) use ($modulo) {
                $j->on('inscripciones.participante_id', '=', 'participantes.id')
                    ->where('inscripciones.programa_id', '=', $modulo->programa_id);
            })
            ->where('modulos_participantes.modulo_id', $moduloId)
            ->select(
                'modulos_participantes.id as mp_id',
                'participantes.id',
                'participantes.ci',
                'participantes.apellidos',
                'participantes.nombres',
                'participantes.celular',
                'modulos_participantes.nota_modulo',
                'modulos_participantes.nota_nivelacion',
                'inscripciones.estado',
                'inscripciones.baja_operativa',
                'inscripciones.baja_academica',
                'inscripciones.baja_contable'
            )
            ->orderBy('participantes.apellidos')
            ->orderBy('participantes.nombres')
            ->get()
            ->unique('id');

        $tieneNivelacion = $participantes->contains(fn ($p) => $p->nota_nivelacion !== null && (float) $p->nota_nivelacion > 0);
        $mensajes = [];

        foreach ($participantes as $p) {
            $nm = $p->nota_modulo !== null ? (float) $p->nota_modulo : null;
            $nn = $p->nota_nivelacion !== null && (float) $p->nota_nivelacion > 0 ? (float) $p->nota_nivelacion : null;
            $tieneNota = $nm !== null && $nm > 0;
            $nombre = self::limpio($p->nombres.' '.$p->apellidos);
            $celular = preg_replace('/[^0-9]/', '', $p->celular ?? '');
            if (strlen($celular) === 8) {
                $celular = '591'.$celular;
            }

            $base = [
                'participante_id' => $p->id,
                'mp_id' => $p->mp_id,
                'nombres' => $p->nombres,
                'apellidos' => $p->apellidos,
                'celular' => $celular,
                'nota' => $nm,
                'nota_nivelacion' => $nn,
                'baja' => BajaInscripcion::motivo($p),
            ];

            if ($modo === 'regular') {
                if (! $tieneNota) {
                    $mensajes[] = $base + ['texto' => null, 'tipo' => 'sin_nota'];

                    continue;
                }
                $mensajes[] = $base + [
                    'tipo' => $nm >= self::APROBACION ? 'aprobado' : 'reprobado',
                    'texto' => self::textoRegular($nombre, $moduloNombre, $programaNombre, $nm),
                ];

                continue;
            }

            // Nivelación: solo quienes reprobaron la nota regular.
            if (! $tieneNota || $nm >= self::APROBACION) {
                continue;
            }
            $aprobo = $nn !== null && $nn >= self::APROBACION;
            $mensajes[] = $base + [
                'tipo' => $aprobo ? 'aprobado' : 'reprobado',
                'sin_nivelacion' => $nn === null,
                'texto' => self::textoNivelacion($nombre, $moduloNombre, $programaNombre, $nn),
            ];
        }

        return ['modulo' => $modulo, 'programa' => $programa, 'tiene_nivelacion' => $tieneNivelacion, 'mensajes' => $mensajes];
    }

    public static function textoRegular(string $nombre, string $modulo, string $programa, float $nota): string
    {
        $n = self::nota($nota);

        if ($nota >= self::APROBACION) {
            return "Hola {$nombre}, has aprobado el módulo *{$modulo}* del programa *{$programa}* con nota *{$n}*. "
                .'Ingresa a la plataforma para verificar tu nota.';
        }

        return "Hola {$nombre}, tu nota en el módulo *{$modulo}* del programa *{$programa}* es *{$n}* y no alcanza la nota de aprobación (".self::APROBACION.').'
            ."\n\nDebes realizar la *nivelación* del módulo: está atento/a al comunicado de la actividad de nivelación en la plataforma.";
    }

    public static function textoNivelacion(string $nombre, string $modulo, string $programa, ?float $nota): string
    {
        if ($nota !== null && $nota >= self::APROBACION) {
            return "Hola {$nombre}, has aprobado la nivelación del módulo *{$modulo}* del programa *{$programa}* con nota *".self::nota($nota).'*. '
                .'La nota máxima de la nivelación es '.self::APROBACION.'.';
        }

        $resultado = $nota === null
            ? "no registras nota de la nivelación del módulo *{$modulo}* del programa *{$programa}*, por lo que no aprobaste el módulo"
            : "tu nota de nivelación del módulo *{$modulo}* del programa *{$programa}* es *".self::nota($nota).'* y no alcanza la nota de aprobación ('.self::APROBACION.')';

        return "Hola {$nombre}, {$resultado}."
            ."\n\nComo reprobaste nuevamente, la siguiente nivelación (2da instancia) *tiene un costo*. "
            .'Comunícate con el responsable del programa y con el área contable para coordinar el pago y la fecha.';
    }

    /** Sin espacios repetidos, tabuladores ni saltos de línea. */
    public static function limpio(?string $texto): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', (string) $texto));
    }

    private static function nota(float $n): string
    {
        return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    }
}
