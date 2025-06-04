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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    // Método para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido1 .
            ($this->apellido2 ? ' ' . $this->apellido2 : '');
    }

    // Relación many-to-many con Curso usando la tabla intermedia 'participacion'
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'participacion', 'persona_id', 'curso_id')
            ->withPivot('rol_participacion_id', 'estado')
            ->withTimestamps();
    }
}
