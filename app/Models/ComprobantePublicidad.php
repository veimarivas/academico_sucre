<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantePublicidad extends Model
{
    protected $table = 'marketing_comprobantes_publicidad';

    protected $fillable = [
        'asesor_id', 'presupuesto_id', 'programa_id', 'gestion', 'mes', 'quincena',
        'fecha_inicio', 'fecha_fin', 'monto', 'descripcion', 'plataforma',
        'campana_nombre', 'enlace_campana', 'pagina_facebook',
        'archivo_path', 'archivo_original', 'archivo_mime',
        'archivo_debito_path', 'archivo_debito_original', 'archivo_debito_mime',
        'monto_aprobado',
        'estado', 'verificado_por', 'verificado_at', 'observaciones_contable',
        'created_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'verificado_at' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'asesor_id');
    }

    public function presupuesto()
    {
        return $this->belongsTo(PresupuestoPublicidad::class, 'presupuesto_id');
    }

    public function programa()
    {
        return $this->belongsTo(\App\Models\Programa::class, 'programa_id');
    }

    public function verificador()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    public static function calcularQuincena(string $fecha): int
    {
        return ((int) date('d', strtotime($fecha))) <= 15 ? 1 : 2;
    }
}
