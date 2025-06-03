<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolParticipacion extends Model
{
    protected $table = 'roles_participacion';

    protected $fillable = [
        'nombre',
    ];
}
