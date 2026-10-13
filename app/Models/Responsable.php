<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responsable extends Model
{
    protected $table = 'responsables';
    public $timestamps = false;

    protected $fillable = [
        'ci', 'nombres', 'apellidos', 'correo', 'color',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'responsable_id');
    }
}
