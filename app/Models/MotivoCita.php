<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotivoCita extends Model
{
    protected $table = 'motivos_cita';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion_minutos',
        'categoria',
        'activo',
        'tipo_sistema' // 'academico', 'medico', 'general'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'duracion_minutos' => 'integer'
    ];

    /**
     * Obtiene todos los motivos activos para un sistema específico
     */
    public static function getMotivosActivos($tipoSistema = 'academico')
    {
        return self::where('activo', true)
                   ->where('tipo_sistema', $tipoSistema)
                   ->orderBy('categoria')
                   ->orderBy('nombre')
                   ->get();
    }

    /**
     * Obtiene motivos agrupados por categoría
     */
    public static function getMotivosAgrupados($tipoSistema = 'academico')
    {
        $motivos = self::getMotivosActivos($tipoSistema);
        
        return $motivos->groupBy('categoria');
    }

    /**
     * Obtiene motivos en formato para select HTML
     */
    public static function getMotivosParaSelect($tipoSistema = 'academico')
    {
        $motivos = self::getMotivosAgrupados($tipoSistema);
        $options = [];
        
        foreach ($motivos as $categoria => $motivosCategoria) {
            $options[$categoria] = $motivosCategoria->map(function ($motivo) {
                return [
                    'value' => $motivo->id,
                    'text' => $motivo->nombre . ' (' . self::formatearDuracion($motivo->duracion_minutos) . ')',
                    'duration' => $motivo->duracion_minutos
                ];
            });
        }
        
        return $options;
    }

    /**
     * Formatea la duración en formato legible
     */
    public static function formatearDuracion($minutos)
    {
        if ($minutos < 60) {
            return $minutos . ' min';
        } elseif ($minutos === 60) {
            return '1 hora';
        } else {
            $horas = floor($minutos / 60);
            $minutosRestantes = $minutos % 60;
            
            if ($minutosRestantes === 0) {
                return $horas . ' hora' . ($horas > 1 ? 's' : '');
            } else {
                return $horas . ' hora' . ($horas > 1 ? 's' : '') . ' ' . $minutosRestantes . ' min';
            }
        }
    }

    /**
     * Obtiene motivos predefinidos para el sistema académico
     */
    public static function getMotivosAcademicos()
    {
        return [
            'Consultas Académicas' => [
                ['id' => 'tutoria', 'nombre' => 'Tutoría académica', 'duracion' => 60],
                ['id' => 'revision_examen', 'nombre' => 'Revisión de examen', 'duracion' => 30],
                ['id' => 'revision_proyecto', 'nombre' => 'Revisión de proyecto', 'duracion' => 45],
                ['id' => 'duda_asignatura', 'nombre' => 'Duda sobre asignatura', 'duracion' => 30],
                ['id' => 'orientacion_academica', 'nombre' => 'Orientación académica', 'duracion' => 60],
            ],
            'Consultas Específicas' => [
                ['id' => 'problema_tecnico', 'nombre' => 'Problema técnico', 'duracion' => 30],
                ['id' => 'planificacion_estudio', 'nombre' => 'Planificación de estudio', 'duracion' => 45],
                ['id' => 'evaluacion_rendimiento', 'nombre' => 'Evaluación de rendimiento', 'duracion' => 60],
                ['id' => 'consejo_carrera', 'nombre' => 'Consejo de carrera', 'duracion' => 90],
            ],
            'Otros' => [
                ['id' => 'personalizado', 'nombre' => 'Motivo personalizado', 'duracion' => 60],
            ]
        ];
    }

    /**
     * Obtiene motivos predefinidos para el sistema médico
     */
    public static function getMotivosMedicos()
    {
        return [
            'Consultas Generales' => [
                ['id' => 'consulta_general', 'nombre' => 'Consulta general', 'duracion' => 30],
                ['id' => 'revision_medica', 'nombre' => 'Revisión médica', 'duracion' => 45],
                ['id' => 'seguimiento', 'nombre' => 'Seguimiento', 'duracion' => 30],
            ],
            'Especialidades' => [
                ['id' => 'cardiologia', 'nombre' => 'Cardiología', 'duracion' => 60],
                ['id' => 'dermatologia', 'nombre' => 'Dermatología', 'duracion' => 30],
                ['id' => 'ginecologia', 'nombre' => 'Ginecología', 'duracion' => 45],
                ['id' => 'pediatria', 'nombre' => 'Pediatría', 'duracion' => 45],
            ],
            'Otros' => [
                ['id' => 'personalizado', 'nombre' => 'Motivo personalizado', 'duracion' => 30],
            ]
        ];
    }
} 