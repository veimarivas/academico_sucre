<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    protected $table = 'asesores';
    public $timestamps = false;

    protected $fillable = [
        'ci', 'nombres', 'apellidos', 'sexo', 'celular',
        'correo', 'estado', 'cargo_id', 'foto',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'asesor_id');
    }
}
