<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoAutogestion extends Model
{
    protected $table = 'marketing_gastos_autogestion';

    protected $fillable = [
        'asesor_id', 'gestion', 'mes', 'fecha_inicio', 'fecha_fin',
        'monto', 'descripcion', 'plataforma', 'campana_nombre',
        'enlace_campana', 'pagina_facebook', 'programa_id',
        'archivo_path', 'archivo_original', 'archivo_mime',
        'archivo_debito_path', 'archivo_debito_original', 'archivo_debito_mime',
        'estado', 'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'asesor_id');
    }

    public function programa()
    {
        return $this->belongsTo(Programa::class, 'programa_id');
    }
}
