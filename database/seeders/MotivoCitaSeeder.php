<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MotivoCita;

class MotivoCitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Motivos académicos
        $motivosAcademicos = [
            // Consultas Académicas
            ['nombre' => 'Tutoría académica', 'descripcion' => 'Sesión de tutoría para resolver dudas académicas', 'duracion_minutos' => 60, 'categoria' => 'Consultas Académicas', 'tipo_sistema' => 'academico'],
            ['nombre' => 'Revisión de examen', 'descripcion' => 'Revisión de examen o evaluación', 'duracion_minutos' => 30, 'categoria' => 'Consultas Académicas', 'tipo_sistema' => 'academico'],
            ['nombre' => 'Revisión de proyecto', 'descripcion' => 'Revisión de proyecto o trabajo final', 'duracion_minutos' => 45, 'categoria' => 'Consultas Académicas', 'tipo_sistema' => 'academico'],
            ['nombre' => 'Duda sobre asignatura', 'descripcion' => 'Consulta sobre contenido específico de una asignatura', 'duracion_minutos' => 30, 'categoria' => 'Consultas Académicas', 'tipo_sistema' => 'academico'],
            ['nombre' => 'Orientación académica', 'descripcion' => 'Orientación sobre plan de estudios y materias', 'duracion_minutos' => 60, 'categoria' => 'Consultas Académicas', 'tipo_sistema' => 'academico'],
            
            // Consultas Específicas
            ['nombre' => 'Problema técnico', 'descripcion' => 'Problema con plataforma o herramientas tecnológicas', 'duracion_minutos' => 30, 'categoria' => 'Consultas Específicas', 'tipo_sistema' => 'academico'],
            ['nombre' => 'Planificación de estudio', 'descripcion' => 'Ayuda para organizar el estudio y horarios', 'duracion_minutos' => 45, 'categoria' => 'Consultas Específicas', 'tipo_sistema' => 'academico'],
            ['nombre' => 'Evaluación de rendimiento', 'descripcion' => 'Evaluación del progreso académico del estudiante', 'duracion_minutos' => 60, 'categoria' => 'Consultas Específicas', 'tipo_sistema' => 'academico'],
            ['nombre' => 'Consejo de carrera', 'descripcion' => 'Orientación sobre futuro profesional y carrera', 'duracion_minutos' => 90, 'categoria' => 'Consultas Específicas', 'tipo_sistema' => 'academico'],
            
            // Otros
            ['nombre' => 'Motivo personalizado', 'descripcion' => 'Consulta con motivo específico personalizado', 'duracion_minutos' => 60, 'categoria' => 'Otros', 'tipo_sistema' => 'academico'],
        ];

        // Motivos médicos (para futuras aplicaciones)
        $motivosMedicos = [
            // Consultas Generales
            ['nombre' => 'Consulta general', 'descripcion' => 'Consulta médica general', 'duracion_minutos' => 30, 'categoria' => 'Consultas Generales', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Revisión médica', 'descripcion' => 'Revisión médica de rutina', 'duracion_minutos' => 45, 'categoria' => 'Consultas Generales', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Seguimiento', 'descripcion' => 'Seguimiento de tratamiento o condición', 'duracion_minutos' => 30, 'categoria' => 'Consultas Generales', 'tipo_sistema' => 'medico'],
            
            // Especialidades
            ['nombre' => 'Cardiología', 'descripcion' => 'Consulta de cardiología', 'duracion_minutos' => 60, 'categoria' => 'Especialidades', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Dermatología', 'descripcion' => 'Consulta de dermatología', 'duracion_minutos' => 30, 'categoria' => 'Especialidades', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Ginecología', 'descripcion' => 'Consulta de ginecología', 'duracion_minutos' => 45, 'categoria' => 'Especialidades', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Pediatría', 'descripcion' => 'Consulta de pediatría', 'duracion_minutos' => 45, 'categoria' => 'Especialidades', 'tipo_sistema' => 'medico'],
            
            // Otros médicos
            ['nombre' => 'Motivo personalizado', 'descripcion' => 'Consulta médica con motivo específico', 'duracion_minutos' => 30, 'categoria' => 'Otros', 'tipo_sistema' => 'medico'],
        ];

        // Insertar motivos académicos
        foreach ($motivosAcademicos as $motivo) {
            MotivoCita::updateOrCreate(
                ['nombre' => $motivo['nombre'], 'tipo_sistema' => $motivo['tipo_sistema']],
                $motivo
            );
        }

        // Insertar motivos médicos (comentados por ahora, se activarán cuando se necesite)
        /*
        foreach ($motivosMedicos as $motivo) {
            MotivoCita::updateOrCreate(
                ['nombre' => $motivo['nombre'], 'tipo_sistema' => $motivo['tipo_sistema']],
                $motivo
            );
        }
        */
    }
} 