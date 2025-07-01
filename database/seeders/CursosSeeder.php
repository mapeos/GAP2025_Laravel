<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Curso;
use Carbon\Carbon;

class CursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la fecha actual para calcular fechas futuras
        $hoy = Carbon::now();
        
        $cursos = [
            [
                'titulo' => 'Introducción a Laravel',
                'descripcion' => 'Curso básico para aprender Laravel desde cero. Ideal para desarrolladores que quieren iniciarse en este framework PHP.',
                'fechaInicio' => $hoy->copy()->addDays(30)->format('Y-m-d'),
                'fechaFin' => $hoy->copy()->addDays(60)->format('Y-m-d'),
                'plazas' => 30,
                'estado' => 'activo',
                'precio' => 299.99,
            ],
            [
                'titulo' => 'PHP Avanzado',
                'descripcion' => 'Profundiza en el desarrollo backend con PHP. Patrones de diseño, arquitectura y mejores prácticas.',
                'fechaInicio' => $hoy->copy()->addDays(45)->format('Y-m-d'),
                'fechaFin' => $hoy->copy()->addDays(75)->format('Y-m-d'),
                'plazas' => 25,
                'estado' => 'activo',
                'precio' => 399.99,
            ],
            [
                'titulo' => 'Desarrollo Web Frontend',
                'descripcion' => 'HTML, CSS y JavaScript para principiantes. Aprende a crear sitios web modernos y responsivos.',
                'fechaInicio' => $hoy->copy()->addDays(60)->format('Y-m-d'),
                'fechaFin' => $hoy->copy()->addDays(90)->format('Y-m-d'),
                'plazas' => 20,
                'estado' => 'activo',
                'precio' => 249.99,
            ],
            [
                'titulo' => 'React.js Completo',
                'descripcion' => 'Desarrollo de aplicaciones web modernas con React.js. Hooks, Context API y Redux.',
                'fechaInicio' => $hoy->copy()->addDays(90)->format('Y-m-d'),
                'fechaFin' => $hoy->copy()->addDays(120)->format('Y-m-d'),
                'plazas' => 15,
                'estado' => 'inactivo',
                'precio' => 449.99,
            ],
            [
                'titulo' => 'Node.js y Express',
                'descripcion' => 'Desarrollo backend con Node.js y Express. APIs RESTful, autenticación y bases de datos.',
                'fechaInicio' => $hoy->copy()->addDays(120)->format('Y-m-d'),
                'fechaFin' => $hoy->copy()->addDays(150)->format('Y-m-d'),
                'plazas' => 18,
                'estado' => 'activo',
                'precio' => 379.99,
            ],
        ];

        foreach ($cursos as $cursoData) {
            Curso::firstOrCreate(
                ['titulo' => $cursoData['titulo']],
                $cursoData
            );
        }
        
        $this->command->info('✅ Cursos creados/verificados con fechas futuras.');
    }
}
