<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantePago extends Model
{
    protected $table = 'comprobantes_pago';

    protected $fillable = [
        'programa_id', 'participante_id', 'inscripcion_id', 'asesor_id',
        'monto', 'archivo_path', 'archivo_original', 'archivo_mime',
        'deudas_seleccionadas', 'estado', 'verificado_por', 'verificado_at',
        'observaciones',
    ];

    protected $casts = [
        'deudas_seleccionadas' => 'array',
        'verificado_at' => 'datetime',
    ];
}
