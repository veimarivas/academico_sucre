<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Configuración clave/valor del sistema (p. ej. número de WhatsApp autorizado).
 */
class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor'];

    public static function get(string $clave, ?string $default = null): ?string
    {
        return static::query()->where('clave', $clave)->value('valor') ?? $default;
    }

    public static function set(string $clave, ?string $valor): void
    {
        static::query()->updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
