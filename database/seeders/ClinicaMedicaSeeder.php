<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EspecialidadMedica;
use App\Models\TratamientoMedico;
use App\Models\Facultativo;
use App\Models\User;
use App\Models\MotivoCita;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ClinicaMedicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        Role::firstOrCreate(['name' => 'Facultativo']);
        Role::firstOrCreate(['name' => 'Paciente']);

        // Crear especialidades médicas
        $especialidades = [
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Atención médica general y preventiva',
                'color' => '#4CAF50'
            ],
            [
                'nombre' => 'Cardiología',
                'descripcion' => 'Especialidad del corazón y sistema cardiovascular',
                'color' => '#F44336'
            ],
            [
                'nombre' => 'Dermatología',
                'descripcion' => 'Especialidad de la piel y enfermedades cutáneas',
                'color' => '#FF9800'
            ],
            [
                'nombre' => 'Ginecología',
                'descripcion' => 'Salud reproductiva femenina',
                'color' => '#E91E63'
            ],
            [
                'nombre' => 'Pediatría',
                'descripcion' => 'Atención médica infantil',
                'color' => '#2196F3'
            ],
            [
                'nombre' => 'Psicología',
                'descripcion' => 'Salud mental y bienestar psicológico',
                'color' => '#9C27B0'
            ],
            [
                'nombre' => 'Traumatología',
                'descripcion' => 'Lesiones y problemas del sistema músculo-esquelético',
                'color' => '#795548'
            ]
        ];

        foreach ($especialidades as $especialidad) {
            EspecialidadMedica::firstOrCreate(
                ['nombre' => $especialidad['nombre']],
                $especialidad
            );
        }

        // Crear tratamientos médicos
        $tratamientos = [
            [
                'nombre' => 'Consulta General',
                'especialidad_id' => 1,
                'duracion_minutos' => 30,
                'costo' => 50.00,
                'descripcion' => 'Consulta médica general de rutina'
            ],
            [
                'nombre' => 'Consulta Especializada',
                'especialidad_id' => 2,
                'duracion_minutos' => 45,
                'costo' => 80.00,
                'descripcion' => 'Consulta con especialista en cardiología'
            ],
            [
                'nombre' => 'Revisión Dermatológica',
                'especialidad_id' => 3,
                'duracion_minutos' => 40,
                'costo' => 70.00,
                'descripcion' => 'Revisión de la piel y diagnóstico dermatológico'
            ],
            [
                'nombre' => 'Consulta Ginecológica',
                'especialidad_id' => 4,
                'duracion_minutos' => 60,
                'costo' => 90.00,
                'descripcion' => 'Consulta ginecológica completa'
            ],
            [
                'nombre' => 'Consulta Pediátrica',
                'especialidad_id' => 5,
                'duracion_minutos' => 35,
                'costo' => 60.00,
                'descripcion' => 'Consulta médica para niños'
            ],
            [
                'nombre' => 'Sesión Psicológica',
                'especialidad_id' => 6,
                'duracion_minutos' => 50,
                'costo' => 75.00,
                'descripcion' => 'Sesión de terapia psicológica'
            ],
            [
                'nombre' => 'Consulta Traumatológica',
                'especialidad_id' => 7,
                'duracion_minutos' => 45,
                'costo' => 85.00,
                'descripcion' => 'Consulta para lesiones y problemas óseos'
            ],
            [
                'nombre' => 'Electrocardiograma',
                'especialidad_id' => 2,
                'duracion_minutos' => 30,
                'costo' => 120.00,
                'descripcion' => 'Prueba de electrocardiograma'
            ],
            [
                'nombre' => 'Ecografía',
                'especialidad_id' => 1,
                'duracion_minutos' => 45,
                'costo' => 150.00,
                'descripcion' => 'Prueba de ecografía general'
            ],
            [
                'nombre' => 'Análisis de Sangre',
                'especialidad_id' => 1,
                'duracion_minutos' => 15,
                'costo' => 40.00,
                'descripcion' => 'Extracción y análisis de sangre'
            ],
            [
                'nombre' => 'Radiografía',
                'especialidad_id' => 7,
                'duracion_minutos' => 20,
                'costo' => 80.00,
                'descripcion' => 'Prueba de radiografía'
            ],
            [
                'nombre' => 'Biopsia',
                'especialidad_id' => 3,
                'duracion_minutos' => 60,
                'costo' => 200.00,
                'descripcion' => 'Procedimiento de biopsia'
            ],
            [
                'nombre' => 'Fisioterapia',
                'especialidad_id' => 7,
                'duracion_minutos' => 60,
                'costo' => 65.00,
                'descripcion' => 'Sesión de fisioterapia'
            ],
            [
                'nombre' => 'Vacunación',
                'especialidad_id' => 5,
                'duracion_minutos' => 15,
                'costo' => 25.00,
                'descripcion' => 'Aplicación de vacunas'
            ],
            [
                'nombre' => 'Control Prenatal',
                'especialidad_id' => 4,
                'duracion_minutos' => 45,
                'costo' => 95.00,
                'descripcion' => 'Control médico durante el embarazo'
            ]
        ];

        foreach ($tratamientos as $tratamiento) {
            TratamientoMedico::firstOrCreate(
                ['nombre' => $tratamiento['nombre'], 'especialidad_id' => $tratamiento['especialidad_id']],
                $tratamiento
            );
        }

        // Crear motivos de cita médicos usando tipos de consulta generales
        $motivosCita = [
            // Consultas Generales
            ['nombre' => 'Medicina General', 'descripcion' => 'Consulta médica general', 'duracion_minutos' => 30, 'categoria' => 'Consultas Generales', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Revisión de Rutina', 'descripcion' => 'Revisión médica de rutina', 'duracion_minutos' => 30, 'categoria' => 'Consultas Generales', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Control de Presión', 'descripcion' => 'Control de presión arterial', 'duracion_minutos' => 20, 'categoria' => 'Consultas Generales', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Consulta por Síntomas', 'descripcion' => 'Consulta por síntomas generales', 'duracion_minutos' => 30, 'categoria' => 'Consultas Generales', 'tipo_sistema' => 'medico'],
            
            // Cardiología
            ['nombre' => 'Cardiología', 'descripcion' => 'Consulta cardiológica', 'duracion_minutos' => 45, 'categoria' => 'Cardiología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Revisión Cardíaca', 'descripcion' => 'Revisión cardíaca completa', 'duracion_minutos' => 45, 'categoria' => 'Cardiología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Control Cardíaco', 'descripcion' => 'Control cardíaco de rutina', 'duracion_minutos' => 30, 'categoria' => 'Cardiología', 'tipo_sistema' => 'medico'],
            
            // Dermatología
            ['nombre' => 'Dermatología', 'descripcion' => 'Consulta dermatológica', 'duracion_minutos' => 40, 'categoria' => 'Dermatología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Revisión de Piel', 'descripcion' => 'Revisión dermatológica', 'duracion_minutos' => 40, 'categoria' => 'Dermatología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Control Dermatológico', 'descripcion' => 'Control dermatológico de rutina', 'duracion_minutos' => 30, 'categoria' => 'Dermatología', 'tipo_sistema' => 'medico'],
            
            // Ginecología
            ['nombre' => 'Ginecología', 'descripcion' => 'Consulta ginecológica', 'duracion_minutos' => 60, 'categoria' => 'Ginecología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Control Ginecológico', 'descripcion' => 'Control ginecológico anual', 'duracion_minutos' => 60, 'categoria' => 'Ginecología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Revisión Ginecológica', 'descripcion' => 'Revisión ginecológica completa', 'duracion_minutos' => 60, 'categoria' => 'Ginecología', 'tipo_sistema' => 'medico'],
            
            // Pediatría
            ['nombre' => 'Pediatría', 'descripcion' => 'Consulta pediátrica', 'duracion_minutos' => 35, 'categoria' => 'Pediatría', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Control Pediátrico', 'descripcion' => 'Control pediátrico de rutina', 'duracion_minutos' => 35, 'categoria' => 'Pediatría', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Revisión Infantil', 'descripcion' => 'Revisión médica infantil', 'duracion_minutos' => 35, 'categoria' => 'Pediatría', 'tipo_sistema' => 'medico'],
            
            // Psicología
            ['nombre' => 'Psicología', 'descripcion' => 'Consulta psicológica', 'duracion_minutos' => 50, 'categoria' => 'Psicología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Terapia Psicológica', 'descripcion' => 'Sesión de terapia psicológica', 'duracion_minutos' => 50, 'categoria' => 'Psicología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Control Psicológico', 'descripcion' => 'Control psicológico de rutina', 'duracion_minutos' => 30, 'categoria' => 'Psicología', 'tipo_sistema' => 'medico'],
            
            // Traumatología
            ['nombre' => 'Traumatología', 'descripcion' => 'Consulta traumatológica', 'duracion_minutos' => 45, 'categoria' => 'Traumatología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Revisión Traumatológica', 'descripcion' => 'Revisión traumatológica completa', 'duracion_minutos' => 45, 'categoria' => 'Traumatología', 'tipo_sistema' => 'medico'],
            ['nombre' => 'Control Traumatológico', 'descripcion' => 'Control traumatológico de rutina', 'duracion_minutos' => 30, 'categoria' => 'Traumatología', 'tipo_sistema' => 'medico']
        ];

        foreach ($motivosCita as $motivo) {
            MotivoCita::firstOrCreate(
                ['nombre' => $motivo['nombre'], 'tipo_sistema' => $motivo['tipo_sistema']],
                $motivo
            );
        }

        // Usar solo el usuario paciente@academia.com como paciente de prueba
        $paciente = User::firstOrCreate(
            ['email' => 'paciente@academia.com'],
            [
                'name' => 'María García',
                'email' => 'paciente@academia.com',
                'password' => Hash::make('password'),
            ]
        );
        $paciente->assignRole('Paciente');

        // Usar solo el usuario medico@academia.com como facultativo de prueba
        $facultativo = User::firstOrCreate(
            ['email' => 'medico@academia.com'],
            [
                'name' => 'Dr. Daniel Sancho',
                'email' => 'medico@academia.com',
                'password' => Hash::make('password'),
            ]
        );
        $facultativo->assignRole('Facultativo');

        // Crear registro de facultativo
        Facultativo::firstOrCreate(
            ['numero_colegiado' => 'M-12345'],
            [
                'user_id' => $facultativo->id,
                'numero_colegiado' => 'M-12345',
                'especialidad_id' => 1, // Medicina General
                'horario_inicio' => '09:00:00',
                'horario_fin' => '17:00:00',
                'activo' => true
            ]
        );

        $this->command->info('Seeder de Clínica Médica ejecutado correctamente.');
        $this->command->info('- 7 especialidades médicas creadas');
        $this->command->info('- 15 tratamientos médicos creados');
        $this->command->info('- 16 motivos de cita creados');
        $this->command->info('- 1 paciente de prueba creado (paciente@test.com)');
        $this->command->info('- 1 facultativo de prueba creado (dr.sancho@test.com)');
    }
} 