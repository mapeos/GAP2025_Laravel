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

    /**
     * Crear una participación de forma segura evitando duplicados
     * 
     * @param int $cursoId
     * @param int $personaId
     * @param int $rolParticipacionId
     * @param string $estado
     * @return Participacion|null
     */
    public static function crearParticipacionSegura($cursoId, $personaId, $rolParticipacionId, $estado = 'pendiente')
    {
        try {
            // Verificar si ya existe la participación
            $participacionExistente = static::where('curso_id', $cursoId)
                ->where('persona_id', $personaId)
                ->first();
            
            if ($participacionExistente) {
                \Illuminate\Support\Facades\Log::warning('[PARTICIPACION] Ya existe participación', [
                    'curso_id' => $cursoId,
                    'persona_id' => $personaId,
                    'estado_actual' => $participacionExistente->estado
                ]);
                return null;
            }
            
            // Crear la participación usando updateOrInsert para mayor seguridad
            $participacion = static::updateOrInsert(
                [
                    'curso_id' => $cursoId,
                    'persona_id' => $personaId
                ],
                [
                    'rol_participacion_id' => $rolParticipacionId,
                    'estado' => $estado,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            // Retornar la participación creada
            return static::where('curso_id', $cursoId)
                ->where('persona_id', $personaId)
                ->first();
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[PARTICIPACION] Error al crear participación', [
                'curso_id' => $cursoId,
                'persona_id' => $personaId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    
}
