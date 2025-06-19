<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Curso;

class CursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cursos = [
            [
                'titulo' => 'Introducción a Laravel',
                'descripcion' => 'Curso básico para aprender Laravel desde cero.',
                'fechaInicio' => '2025-06-10',
                'fechaFin' => '2025-07-10',
                'plazas' => 30,
                'estado' => 'activo',
            ],
            [
                'titulo' => 'PHP Avanzado',
                'descripcion' => 'Profundiza en el desarrollo backend con PHP.',
                'fechaInicio' => '2025-07-15',
                'fechaFin' => '2025-08-15',
                'plazas' => 25,
                'estado' => 'activo',
            ],
            [
                'titulo' => 'Desarrollo Web',
                'descripcion' => 'HTML, CSS y JavaScript para principiantes.',
                'fechaInicio' => '2025-08-01',
                'fechaFin' => '2025-09-01',
                'plazas' => 20,
                'estado' => 'inactivo',
            ],
        ];

        foreach ($cursos as $cursoData) {
            Curso::firstOrCreate(
                ['titulo' => $cursoData['titulo']],
                $cursoData
            );
        }
        
        $this->command->info('✅ Cursos creados/verificados.');
    }
}
