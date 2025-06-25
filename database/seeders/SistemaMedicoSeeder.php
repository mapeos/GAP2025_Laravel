<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SistemaMedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar especialidades médicas
        DB::table('especialidades_medicas')->insert([
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Atención médica general y preventiva',
                'color' => '#007bff',
                'activa' => true,
            ],
            [
                'nombre' => 'Cardiología',
                'descripcion' => 'Especialidad del corazón y sistema cardiovascular',
                'color' => '#dc3545',
                'activa' => true,
            ],
            [
                'nombre' => 'Dermatología',
                'descripcion' => 'Especialidad de la piel y enfermedades cutáneas',
                'color' => '#fd7e14',
                'activa' => true,
            ],
            [
                'nombre' => 'Ginecología',
                'descripcion' => 'Salud reproductiva femenina',
                'color' => '#e83e8c',
                'activa' => true,
            ],
            [
                'nombre' => 'Pediatría',
                'descripcion' => 'Atención médica infantil',
                'color' => '#20c997',
                'activa' => true,
            ],
            [
                'nombre' => 'Ortopedia',
                'descripcion' => 'Especialidad del sistema musculoesquelético',
                'color' => '#6f42c1',
                'activa' => true,
            ],
            [
                'nombre' => 'Psicología',
                'descripcion' => 'Salud mental y bienestar psicológico',
                'color' => '#ffc107',
                'activa' => true,
            ],
            [
                'nombre' => 'Oftalmología',
                'descripcion' => 'Especialidad de los ojos y visión',
                'color' => '#17a2b8',
                'activa' => true,
            ],
        ]);

        // Insertar tratamientos médicos
        DB::table('tratamientos_medicos')->insert([
            // Medicina General
            [
                'nombre' => 'Consulta médica general',
                'descripcion' => 'Revisión médica general y evaluación de síntomas',
                'costo' => 50.00,
                'duracion_minutos' => 30,
                'especialidad_id' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Revisión de rutina',
                'descripcion' => 'Control médico preventivo',
                'costo' => 40.00,
                'duracion_minutos' => 20,
                'especialidad_id' => 1,
                'activo' => true,
            ],
            
            // Cardiología
            [
                'nombre' => 'Electrocardiograma',
                'descripcion' => 'Estudio del ritmo cardíaco',
                'costo' => 80.00,
                'duracion_minutos' => 45,
                'especialidad_id' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Consulta cardiológica',
                'descripcion' => 'Evaluación cardiovascular especializada',
                'costo' => 90.00,
                'duracion_minutos' => 60,
                'especialidad_id' => 2,
                'activo' => true,
            ],
            
            // Dermatología
            [
                'nombre' => 'Consulta dermatológica',
                'descripcion' => 'Evaluación de problemas de piel',
                'costo' => 70.00,
                'duracion_minutos' => 45,
                'especialidad_id' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'Biopsia de piel',
                'descripcion' => 'Toma de muestra para análisis',
                'costo' => 120.00,
                'duracion_minutos' => 30,
                'especialidad_id' => 3,
                'activo' => true,
            ],
            
            // Ginecología
            [
                'nombre' => 'Consulta ginecológica',
                'descripcion' => 'Revisión ginecológica general',
                'costo' => 75.00,
                'duracion_minutos' => 45,
                'especialidad_id' => 4,
                'activo' => true,
            ],
            [
                'nombre' => 'Ecografía ginecológica',
                'descripcion' => 'Estudio ecográfico del aparato reproductor',
                'costo' => 150.00,
                'duracion_minutos' => 60,
                'especialidad_id' => 4,
                'activo' => true,
            ],
            
            // Pediatría
            [
                'nombre' => 'Consulta pediátrica',
                'descripcion' => 'Atención médica infantil',
                'costo' => 60.00,
                'duracion_minutos' => 40,
                'especialidad_id' => 5,
                'activo' => true,
            ],
            [
                'nombre' => 'Vacunación infantil',
                'descripcion' => 'Aplicación de vacunas según calendario',
                'costo' => 45.00,
                'duracion_minutos' => 20,
                'especialidad_id' => 5,
                'activo' => true,
            ],
            
            // Ortopedia
            [
                'nombre' => 'Consulta ortopédica',
                'descripcion' => 'Evaluación de problemas musculoesqueléticos',
                'costo' => 85.00,
                'duracion_minutos' => 45,
                'especialidad_id' => 6,
                'activo' => true,
            ],
            [
                'nombre' => 'Radiografía',
                'descripcion' => 'Estudio radiológico',
                'costo' => 90.00,
                'duracion_minutos' => 30,
                'especialidad_id' => 6,
                'activo' => true,
            ],
            
            // Psicología
            [
                'nombre' => 'Sesión de terapia',
                'descripcion' => 'Sesión de psicoterapia individual',
                'costo' => 100.00,
                'duracion_minutos' => 60,
                'especialidad_id' => 7,
                'activo' => true,
            ],
            [
                'nombre' => 'Evaluación psicológica',
                'descripcion' => 'Evaluación completa del estado psicológico',
                'costo' => 150.00,
                'duracion_minutos' => 90,
                'especialidad_id' => 7,
                'activo' => true,
            ],
            
            // Oftalmología
            [
                'nombre' => 'Examen oftalmológico',
                'descripcion' => 'Revisión completa de la vista',
                'costo' => 85.00,
                'duracion_minutos' => 45,
                'especialidad_id' => 8,
                'activo' => true,
            ],
            [
                'nombre' => 'Medición de presión ocular',
                'descripcion' => 'Tonometría para detectar glaucoma',
                'costo' => 65.00,
                'duracion_minutos' => 30,
                'especialidad_id' => 8,
                'activo' => true,
            ],
        ]);

        // Nota: Los facultativos se crearán manualmente o mediante otro seeder
        // ya que requieren usuarios existentes en el sistema
    }
} 