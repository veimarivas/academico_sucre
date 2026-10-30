<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * QR de pago y cuentas bancarias para los mensajes masivos.
 *
 * Solo se puede adjuntar un QR **vigente**: activo y con fecha de vencimiento
 * igual o posterior a hoy. La imagen se guarda en el disco privado y se
 * manda a Seguimiento ESAM como la imagen del mensaje (el texto va de pie).
 */
class PagosQr
{
    public const DISCO = 'local';

    public const CARPETA = 'pagos-qr';

    /** Días antes del vencimiento en que se avisa «por vencer». */
    public const AVISO_DIAS = 7;

    /** QR que se pueden elegir hoy (para los selectores). */
    public static function vigentes(): Collection
    {
        return DB::table('pagos_qr')
            ->where('activo', true)
            ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
            ->orderBy('fecha_vencimiento')
            ->get()
            ->map(fn ($q) => self::conEstado($q));
    }

    /** Estado legible: vigente, por_vencer, vencido o inactivo, con los días que faltan. */
    public static function conEstado(object $q): object
    {
        $dias = (int) now()->startOfDay()->diffInDays(\Illuminate\Support\Carbon::parse($q->fecha_vencimiento)->startOfDay(), false);
        $q->dias_para_vencer = $dias;
        $q->estado = ! $q->activo ? 'inactivo' : ($dias < 0 ? 'vencido' : ($dias <= self::AVISO_DIAS ? 'por_vencer' : 'vigente'));
        $q->imagen_url = route('admin.pagos-qr.imagen', $q->id);

        return $q;
    }

    /**
     * El QR elegido para un envío, que debe estar vigente. Sin id: null.
     *
     * @throws ValidationException si no existe, está inactivo o vencido.
     */
    public static function elegido($id): ?object
    {
        if ($id === null || $id === '' || (int) $id === 0) {
            return null;
        }
        $q = DB::table('pagos_qr')->where('id', (int) $id)->first();
        if (! $q) {
            throw ValidationException::withMessages(['qr_id' => 'El QR elegido ya no existe.']);
        }
        $q = self::conEstado($q);
        if ($q->estado === 'inactivo') {
            throw ValidationException::withMessages(['qr_id' => "El QR «{$q->nombre}» está desactivado: elegí otro."]);
        }
        if ($q->estado === 'vencido') {
            throw ValidationException::withMessages(['qr_id' => "El QR «{$q->nombre}» venció el ".\Illuminate\Support\Carbon::parse($q->fecha_vencimiento)->format('d/m/Y').': elegí uno vigente.']);
        }

        return $q;
    }

    /** La imagen del QR como adjunto para Seguimiento ESAM. */
    public static function adjunto(object $q): array
    {
        $disk = Storage::disk(self::DISCO);
        if (! $disk->exists($q->imagen_path)) {
            throw ValidationException::withMessages(['qr_id' => "No se encontró la imagen del QR «{$q->nombre}»."]);
        }

        return [
            'base64' => base64_encode((string) $disk->get($q->imagen_path)),
            'mimetype' => $disk->mimeType($q->imagen_path) ?: 'image/png',
            'filename' => 'QR-'.\Illuminate\Support\Str::slug($q->banco.' '.$q->nombre).'.'.pathinfo($q->imagen_path, PATHINFO_EXTENSION),
        ];
    }

    /** Datos de la cuenta para agregar al final del mensaje. */
    public static function textoCuenta(object $q): string
    {
        $lineas = ['💳 *Datos para el pago*', 'Banco: '.$q->banco];
        if (filled($q->numero_cuenta)) {
            $lineas[] = 'Cuenta: '.$q->numero_cuenta.(filled($q->tipo_cuenta) ? ' ('.$q->tipo_cuenta.')' : '').(filled($q->moneda) ? ' · '.$q->moneda : '');
        }
        if (filled($q->titular)) {
            $lineas[] = 'Titular: '.$q->titular;
        }

        return implode("\n", $lineas);
    }
}
