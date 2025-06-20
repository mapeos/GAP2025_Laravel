<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Participacion extends Pivot
{
    protected $table = 'participacion';

    public $incrementing = false; // porque la clave primaria es compuesta
    public $timestamps = true;

    protected $primaryKey = ['curso_id', 'persona_id'];

    protected $fillable = [
        'curso_id',
        'persona_id',
        'rol_participacion_id',
        'estado',
    ];

    protected $casts = [
        'curso_id' => 'integer',
        'persona_id' => 'integer',
        'rol_participacion_id' => 'integer',
    ];

    /**
     * Curso al que pertenece esta participación.
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Persona que participa.
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Rol de participación (alumno, profesor, etc).
     */
    public function rol()
    {
        return $this->belongsTo(RolParticipacion::class, 'rol_participacion_id');
    }

    
}
