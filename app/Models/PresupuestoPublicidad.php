<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresupuestoPublicidad extends Model
{
    protected $table = 'marketing_presupuestos_publicidad';

    protected $fillable = [
        'supervisor_id', 'asesor_id', 'presupuesto_origen_id', 'gestion', 'mes', 'quincena',
        'monto_asignado', 'monto_reembolsado', 'estado', 'periodo_cerrado', 'observaciones', 'created_by',
        'archivo_derivacion_path', 'archivo_derivacion_original', 'archivo_derivacion_mime',
    ];

    public function presupuestoOrigen()
    {
        return $this->belongsTo(self::class, 'presupuesto_origen_id');
    }

    public function presupuestosDerivados()
    {
        return $this->hasMany(self::class, 'presupuesto_origen_id');
    }

    protected $casts = [
        'monto_asignado' => 'decimal:2',
        'monto_reembolsado' => 'decimal:2',
    ];

    public function supervisor()
    {
        return $this->belongsTo(Asesor::class, 'supervisor_id');
    }

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'asesor_id');
    }

    public function comprobantes()
    {
        return $this->hasMany(ComprobantePublicidad::class, 'presupuesto_id');
    }
}
