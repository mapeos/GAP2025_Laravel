<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    // Nombre de la tabla, sino sigue la convención plural de Laravel que es 'direccions'
       protected $table = 'direcciones'; 


        protected $fillable = [
        'calle',
        'numero',
        'piso',
        'ciudad',
        'provincia',
        'cp',
        'pais',
    ];
}
