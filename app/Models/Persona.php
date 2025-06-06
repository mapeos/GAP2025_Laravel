<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'dni',
        'tfno',
        'direccion_id',
        'user_id',
    ];
}
