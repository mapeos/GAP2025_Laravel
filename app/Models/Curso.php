<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo de la tabla cursos
 * Ejemplos:
 * - Obtener todos los participantes de un curso: $curso = Curso::with('personas')->find($id);
 * - Obtener solo los profesores del curso: $profesores = $curso->personasPorRol('profesor')->get();
 * - Obtener solo los alumnos del curso: $alumnos = $curso->personasPorRol('alumno')->get();
 */
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
        'precio',
    ];

    protected $dates = ['fechaInicio', 'fechaFin'];

    /**
     * Relación con participaciones (tabla pivote con información adicional)
     */
    public function participaciones()
    {
        return $this->hasMany(Participacion::class, 'curso_id')->with('persona', 'rol');
    }

    /**
     * Participantes del curso (personas a través de la pivote)
     */
    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'participacion', 'curso_id', 'persona_id')
                    ->withPivot('rol_participacion_id', 'estado')
                    ->withTimestamps();
    }

    /**
     * Filtra participantes por rol (ej: profesor, alumno, etc.)
     */
    public function personasPorRol(string $rol)
    {
        return $this->personas()->whereHas('participaciones', function ($query) use ($rol) {
            $query->whereHas('rol', function ($subQuery) use ($rol) {
                $subQuery->where('nombre', $rol);
            });
        });
    }

    /**
     * Cursos que están activos hoy (entre fechaInicio y fechaFin)
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->whereDate('fechaInicio', '<=', now())
                     ->whereDate('fechaFin', '>=', now());
    }

    /**
     * Cursos que todavía no han comenzado
     */
    public function scopeFuturos(Builder $query): Builder
    {
        return $query->whereDate('fechaInicio', '>', now());
    }

    /**
     * Cursos que ya han finalizado
     */
    public function scopeFinalizados(Builder $query): Builder
    {
        return $query->whereDate('fechaFin', '<', now());
    }

    /**
     * Filtrar cursos por estado (ej. "abierto", "cerrado", "cancelado", etc.)
     */
    public function scopeConEstado(Builder $query, string $estado): Builder
    {
        return $query->where('estado', $estado);
    }
    
}