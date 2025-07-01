<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

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
     * Método robusto que maneja casos edge y errores de base de datos
     */
    public function getInscritosCount(): int
    {
        try {
            // Verificar que el curso existe y tiene ID
            if (!$this->exists || !$this->id) {
                return 0;
            }
            
            // Usar una consulta más eficiente y segura
            return $this->personas()
                ->whereHas('participaciones', function ($query) {
                    $query->whereHas('rol', function ($subQuery) {
                        $subQuery->where('nombre', 'Alumno');
                    });
                })
                ->count();
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::warning('Error al contar inscritos del curso ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener el número de plazas disponibles
     * Método robusto que maneja valores nulos o inválidos
     */
    public function getPlazasDisponibles(): int
    {
        try {
            // Verificar que plazas sea un número válido
            $plazas = intval($this->plazas ?? 0);
            if ($plazas <= 0) {
                return 0;
            }
            
            $inscritos = $this->getInscritosCount();
            return max(0, $plazas - $inscritos);
        } catch (\Exception $e) {
            Log::warning('Error al calcular plazas disponibles del curso ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener el porcentaje de ocupación del curso
     * Método robusto que maneja división por cero y valores inválidos
     */
    public function getPorcentajeOcupacion(): float
    {
        try {
            // Verificar que plazas sea un número válido mayor que 0
            $plazas = intval($this->plazas ?? 0);
            if ($plazas <= 0) {
                return 0.0;
            }
            
            $inscritos = $this->getInscritosCount();
            $porcentaje = ($inscritos / $plazas) * 100;
            
            // Redondear a 1 decimal y asegurar que no exceda 100%
            return min(100.0, round($porcentaje, 1));
        } catch (\Exception $e) {
            Log::warning('Error al calcular porcentaje de ocupación del curso ' . $this->id . ': ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Obtener la clase CSS para el color de las plazas según ocupación
     * Método robusto con colores más intuitivos
     */
    public function getPlazasColorClass(): string
    {
        try {
            $porcentaje = $this->getPorcentajeOcupacion();
            
            if ($porcentaje >= 100) {
                return 'text-danger fw-bold'; // Rojo - Curso completo
            } elseif ($porcentaje >= 80) {
                return 'text-warning fw-bold'; // Amarillo - Pocas plazas disponibles
            } elseif ($porcentaje >= 50) {
                return 'text-info fw-bold'; // Azul - Plazas moderadas
            } else {
                return 'text-success fw-bold'; // Verde - Muchas plazas disponibles
            }
        } catch (\Exception $e) {
            Log::warning('Error al obtener clase de color del curso ' . $this->id . ': ' . $e->getMessage());
            return 'text-muted'; // Color gris por defecto en caso de error
        }
    }

    /**
     * Verificar si el curso tiene plazas disponibles
     * Método útil para mostrar/ocultar botones de inscripción
     */
    public function tienePlazasDisponibles(): bool
    {
        return $this->getPlazasDisponibles() > 0;
    }

    /**
     * Verificar si el curso está completo (sin plazas disponibles)
     */
    public function estaCompleto(): bool
    {
        return $this->getPlazasDisponibles() <= 0;
    }

    /**
     * Obtener el estado de disponibilidad del curso como texto
     */
    public function getEstadoDisponibilidad(): string
    {
        if ($this->estaCompleto()) {
            return 'Completo';
        }
        
        $disponibles = $this->getPlazasDisponibles();
        if ($disponibles <= 3) {
            return 'Últimas plazas';
        }
        
        return 'Disponible';
    }

    /**
     * Verificar si el curso ya ha finalizado (fecha fin en el pasado)
     */
    public function haFinalizado(): bool
    {
        return $this->fechaFin < now()->startOfDay();
    }

    /**
     * Verificar si el curso ya ha comenzado (fecha inicio en el pasado)
     */
    public function haComenzado(): bool
    {
        return $this->fechaInicio <= now()->startOfDay();
    }

    /**
     * Verificar si el curso está en progreso (entre fecha inicio y fin)
     */
    public function estaEnProgreso(): bool
    {
        return $this->haComenzado() && !$this->haFinalizado();
    }

    /**
     * Verificar si el curso está en el futuro (no ha comenzado)
     */
    public function estaEnFuturo(): bool
    {
        return $this->fechaInicio > now()->startOfDay();
    }

    /**
     * Obtener el estado temporal del curso como texto
     */
    public function getEstadoTemporal(): string
    {
        if ($this->haFinalizado()) {
            return 'Finalizado';
        } elseif ($this->estaEnProgreso()) {
            return 'En Progreso';
        } elseif ($this->estaEnFuturo()) {
            return 'Próximamente';
        } else {
            return 'Desconocido';
        }
    }
}