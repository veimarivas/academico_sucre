<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'role',
        'responsable_id',
        'asesor_id',
        'whatsapp_numero',
        'whatsapp_area',
        'whatsapp_autorizado',
        'last_activity_at',
    ];

    public const AREAS_WHATSAPP = [
        'contabilidad' => 'Contabilidad',
        'academico' => 'Académico',
        'marketing' => 'Marketing',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'password' => 'hashed',
            'whatsapp_autorizado' => 'boolean',
        ];
    }

    public function getNameAttribute()
    {
        return $this->fullname;
    }

    /**
     * Roles con acceso a las pestañas de marketing del perfil
     * (ofertas en inscripción, inscripciones y presupuesto publicidad).
     */
    public function canAccessMarketing(): bool
    {
        return $this->hasAnyRole([
            'Super Admin', 'Gerencia Marketing', 'Supervisor de Marketing', 'Asesor de Marketing',
        ]);
    }

    /** ¿El usuario es responsable asignado del programa? (puede haber varios). */
    public function esResponsableDelPrograma(int $programaId): bool
    {
        if (empty($this->responsable_id)) {
            return false;
        }

        return \Illuminate\Support\Facades\DB::table('programas_responsables')
            ->where('programa_id', $programaId)
            ->where('responsable_id', $this->responsable_id)
            ->exists();
    }

    /** ¿Pertenece al área contable? */
    public function esAreaContable(): bool
    {
        return $this->hasRole('Contabilidad') || $this->can('contable.cobrar');
    }

    /** ¿Puede ver/enviar por el WhatsApp del programa? */
    public function puedeWhatsappPrograma(int $programaId): bool
    {
        return $this->hasRole('Super Admin') || $this->esResponsableDelPrograma($programaId);
    }

    /** ¿Puede gestionar la baja de una inscripción del programa? */
    public function puedeGestionarBaja(int $programaId): bool
    {
        return $this->hasRole('Super Admin')
            || $this->esAreaContable()
            || $this->esResponsableDelPrograma($programaId);
    }

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'asesor_id');
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class, 'responsable_id');
    }

    public function isOnline(): bool
    {
        if (!$this->last_activity_at) {
            return false;
        }
        return $this->last_activity_at->gt(now()->subMinutes(5));
    }

    public function lastSeen(): ?string
    {
        if (!$this->last_activity_at) {
            return null;
        }

        if ($this->isOnline()) {
            return 'En línea';
        }

        $totalSeconds = $this->last_activity_at->diffInSeconds(now());

        if ($totalSeconds < 60) {
            return 'Justo ahora';
        }

        $minutes = intdiv($totalSeconds, 60);
        $days = intdiv($minutes, 1440);
        $minutes %= 1440;
        $hours = intdiv($minutes, 60);
        $minutes %= 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days} día(s)";
        }
        if ($hours > 0) {
            $parts[] = "{$hours} h";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes} min";
        }

        if ($days >= 7) {
            return $this->last_activity_at->format('d/m/Y H:i');
        }

        return 'Hace ' . implode(' ', $parts);
    }
}