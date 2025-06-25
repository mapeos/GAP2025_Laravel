<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';  // nombre de la tabla

    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_gasto',
        'importe',
        'concepto',
        'fecha',
        'pendiente',
        'tipo_pago', // unico o mensual
        'meses',     // número de meses si es mensual
        'nombre',
        'email',
        'curso',
    ];
}
