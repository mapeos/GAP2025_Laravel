<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de la tabla cursos
 * Ejemplos:
 * - Obtener todos los participantes de un curso: $curso = Curso::with('personas')->find($id);
 * - Obtener solo los profesores del curso: $profesores = $curso->personasPorRol('profesor')->get();
 * - Obtener solo los alumnos del curso: $alumnos = $curso->personasPorRol('alumno')->get();
 */
 class Curso extends Model
{
    use SoftDeletes;

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

    protected $dates = ['fechaInicio', 'fechaFin', 'deleted_at'];

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

    /**
     * Obtener el número de participantes inscritos (solo alumnos)
     */
    public function getInscritosCount(): int
    {
        return $this->personasPorRol('alumno')->count();
    }

    /**
     * Obtener el número de plazas disponibles
     */
    public function getPlazasDisponibles(): int
    {
        return max(0, $this->plazas - $this->getInscritosCount());
    }

    /**
     * Obtener el porcentaje de ocupación del curso
     */
    public function getPorcentajeOcupacion(): float
    {
        if ($this->plazas == 0) return 0;
        return ($this->getInscritosCount() / $this->plazas) * 100;
    }

    /**
     * Obtener la clase CSS para el color de las plazas según ocupación
     */
    public function getPlazasColorClass(): string
    {
        $porcentaje = $this->getPorcentajeOcupacion();
        
        if ($porcentaje >= 90) {
            return 'text-danger fw-bold'; // Rojo - menos del 10% disponible
        } elseif ($porcentaje >= 50) {
            return 'text-warning fw-bold'; // Amarillo - menos de la mitad disponible
        } else {
            return 'text-info'; // Azul claro - más de la mitad disponible
        }
    }
    
}