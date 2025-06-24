<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';  // nombre de la tabla

    protected $fillable = [
        'id_persona',
        'concepto',
        'fecha',
        'importe',
        'pagado',
    ];
}
