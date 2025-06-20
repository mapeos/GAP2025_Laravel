<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Curso;
use App\Models\Persona;
use App\Models\RolParticipacion;

class ParticipacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar los datos por nombre/título
        $cursoLaravel = Curso::where('titulo', 'Introducción a Laravel')->first();
        $cursoPHP = Curso::where('titulo', 'PHP Avanzado')->first();
        
        $personaLuz = Persona::where('nombre', 'Luz')->where('apellido1', 'Cuesta')->first();
        $personaBenito = Persona::where('nombre', 'Benito')->where('apellido1', 'Camela')->first();
        
        $rolProfesor = RolParticipacion::where('nombre', 'Profesor')->first();
        $rolAlumno = RolParticipacion::where('nombre', 'Alumno')->first();

        // Solo crear participaciones si existen todos los datos necesarios
        if ($cursoLaravel && $cursoPHP && $personaLuz && $personaBenito && $rolProfesor && $rolAlumno) {
            $participaciones = [
                [
                    'curso_id' => $cursoLaravel->id,
                    'persona_id' => $personaLuz->id,
                    'rol_participacion_id' => $rolProfesor->id,
                    'estado' => 'activo',
                ],
                [
                    'curso_id' => $cursoPHP->id,
                    'persona_id' => $personaBenito->id,
                    'rol_participacion_id' => $rolAlumno->id,
                    'estado' => 'pendiente',
                ],
            ];

            foreach ($participaciones as $participacion) {
                DB::table('participacion')->updateOrInsert(
                    [
                        'curso_id' => $participacion['curso_id'],
                        'persona_id' => $participacion['persona_id']
                    ],
                    $participacion
                );
            }
            
            $this->command->info('✅ Participaciones creadas/verificadas.');
        } else {
            $this->command->info('⚠️  No se pudieron crear participaciones. Faltan datos necesarios.');
        }
    }
}
