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
        'temario_path',
        'portada_path',
    ];

    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'participacion', 'curso_id', 'persona_id')
                    ->withPivot('rol_participacion_id', 'estado')
                    ->withTimestamps();
    }
}