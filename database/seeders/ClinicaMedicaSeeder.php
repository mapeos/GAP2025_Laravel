<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EspecialidadMedica;
use App\Models\TratamientoMedico;
use App\Models\Facultativo;
use App\Models\User;
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

        // Crear solo 3 especialidades básicas para desarrollo
        $especialidades = [
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Atención médica general y preventiva',
                'color' => '#28a745',
            ],
            [
                'nombre' => 'Cardiología',
                'descripcion' => 'Especialidad del corazón y sistema cardiovascular',
                'color' => '#dc3545',
            ],
            [
                'nombre' => 'Psicología',
                'descripcion' => 'Salud mental y bienestar psicológico',
                'color' => '#6f42c1',
            ],
        ];

        foreach ($especialidades as $especialidad) {
            EspecialidadMedica::firstOrCreate(
                ['nombre' => $especialidad['nombre']],
                $especialidad
            );
        }

        // Crear tratamientos básicos
        $tratamientos = [
            [
                'nombre' => 'Consulta General',
                'descripcion' => 'Consulta médica general de 30 minutos',
                'costo' => 50.00,
                'duracion_minutos' => 30,
                'especialidad_id' => 1,
            ],
            [
                'nombre' => 'Electrocardiograma',
                'descripcion' => 'Estudio del ritmo cardíaco',
                'costo' => 120.00,
                'duracion_minutos' => 60,
                'especialidad_id' => 2,
            ],
            [
                'nombre' => 'Sesión de Psicología',
                'descripcion' => 'Sesión de terapia psicológica',
                'costo' => 100.00,
                'duracion_minutos' => 60,
                'especialidad_id' => 3,
            ],
        ];

        foreach ($tratamientos as $tratamiento) {
            TratamientoMedico::firstOrCreate(
                ['nombre' => $tratamiento['nombre'], 'especialidad_id' => $tratamiento['especialidad_id']],
                $tratamiento
            );
        }

        // Crear SOLO 1 facultativo para desarrollo
        $facultativo = [
            'name' => 'Dr. Daniel Sancho',
            'email' => 'medico@academia.com',
            'password' => 'password',
            'numero_colegiado' => 'M-12345',
            'especialidad_id' => 1, // Medicina General
        ];

        $user = User::firstOrCreate(
            ['email' => $facultativo['email']],
            [
                'name' => $facultativo['name'],
                'email' => $facultativo['email'],
                'password' => Hash::make($facultativo['password']), // Hash the password properly
                'email_verified_at' => now(),
                'status' => 'activo',
            ]
        );

        // Asignar rol Facultativo
        if (!$user->hasRole('Facultativo')) {
            $user->assignRole('Facultativo');
        }

        Facultativo::firstOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'numero_colegiado' => $facultativo['numero_colegiado'],
                'especialidad_id' => $facultativo['especialidad_id'],
                'horario_inicio' => '08:00:00',
                'horario_fin' => '18:00:00',
                'activo' => true,
            ]
        );

        // Crear algunos pacientes (usuarios con rol Paciente) para probar las citas médicas
        $pacientes = [
            [
                'name' => 'Paciente Impacientez',
                'email' => 'paciente@academia.com',
                'password' => 'password',
            ]
        ];

        foreach ($pacientes as $paciente) {
            $user = User::firstOrCreate(
                ['email' => $paciente['email']],
                [
                    'name' => $paciente['name'],
                    'email' => $paciente['email'],
                    'password' => Hash::make($paciente['password']), // Hash the password properly
                    'email_verified_at' => now(),
                    'status' => 'activo',
                ]
            );

            if (!$user->hasRole('Paciente')) {
                $user->assignRole('Paciente');
            }
        }

        $this->command->info('Datos de clínica médica para desarrollo creados exitosamente.');
        $this->command->info('Facultativo: medico@academia.com / password');
        $this->command->info('Pacientes: paciente@academia.com / password');
    }
} 