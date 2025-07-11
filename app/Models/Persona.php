<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'dni', // ahora puede ser null
        'tfno',
        'direccion_id',
        'user_id',
        'foto_perfil', // nuevo campo para la foto de perfil
    ];

    protected $casts = [
        'direccion_id' => 'integer',
        'user_id' => 'integer',
        'dni' => 'string', // asegurar tipo string aunque sea null
    ];


   /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Relación con la dirección 
     */
    public function direccion()
    {
        return $this->belongsTo(Direccion::class)->withDefault();
    }

    /**
     * Relación con participaciones (cursos en los que participa esta persona).
     */
    public function participaciones()
    {
        return $this->hasMany(Participacion::class, 'persona_id')
                    ->with('rol');
    }

    /**
     * Cursos en los que participa (relación many-to-many con datos extra).
     * Esta relación inversa la necesitamos para obtener las personas del curso
     */
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'participacion', 'persona_id', 'curso_id')
                    ->withPivot(['rol_participacion_id', 'estado'])
                    ->withTimestamps();
    }

    /**
     * Accesor completo para el nombre completo de la persona.
     */
    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre} {$this->apellido1} {$this->apellido2}");
    }
}

