<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'fechaInicio',
        'fechaFin',
        'plazas',
        'estado',
    ];

    // Relación many-to-many con Persona
    // Relación many-to-many con Persona usando la tabla intermedia 'participacion'
public function personas()
{
    return $this->belongsToMany(Persona::class, 'participacion', 'curso_id', 'persona_id')
                ->withPivot('rol_participacion_id', 'estado')
                ->withTimestamps();
}
}
