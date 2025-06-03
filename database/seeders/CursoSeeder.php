<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;
use Carbon\Carbon;

class CursoSeeder extends Seeder
{
    public function run()
    {
        $cursos = [
            [
                'titulo' => 'Introducción a la Programación',
                'descripcion' => 'Aprende los fundamentos de la programación con ejemplos prácticos.',
                'fechaInicio' => '2025-09-01',
                'fechaFin' => '2025-12-15',
                'plazas' => 30,
                'estado' => 'abierto',
            ],
            [
                'titulo' => 'Desarrollo Web con Laravel',
                'descripcion' => 'Curso completo de desarrollo web usando el framework Laravel.',
                'fechaInicio' => '2025-10-01',
                'fechaFin' => '2026-01-15',
                'plazas' => 25,
                'estado' => 'abierto',
            ],
            [
                'titulo' => 'Bases de Datos SQL',
                'descripcion' => 'Domina la gestión y consulta de bases de datos relacionales.',
                'fechaInicio' => '2025-09-15',
                'fechaFin' => '2025-12-01',
                'plazas' => 20,
                'estado' => 'abierto',
            ],
            [
                'titulo' => 'Frontend con Angular',
                'descripcion' => 'Desarrolla aplicaciones SPA modernas con Angular.',
                'fechaInicio' => '2025-11-01',
                'fechaFin' => '2026-02-15',
                'plazas' => 20,
                'estado' => 'abierto',
            ],
            [
                'titulo' => 'Python para Ciencia de Datos',
                'descripcion' => 'Introducción a Python y sus librerías para análisis de datos.',
                'fechaInicio' => '2025-09-20',
                'fechaFin' => '2025-12-20',
                'plazas' => 30,
                'estado' => 'abierto',
            ],
            [
                'titulo' => 'Machine Learning Básico',
                'descripcion' => 'Conceptos y algoritmos fundamentales de aprendizaje automático.',
                'fechaInicio' => '2025-10-10',
                'fechaFin' => '2026-01-10',
                'plazas' => 15,
                'estado' => 'abierto',
            ],
            [
                'titulo' => 'Administración de Sistemas Linux',
                'descripcion' => 'Aprende a gestionar servidores y sistemas Linux.',
                'fechaInicio' => '2025-11-05',
                'fechaFin' => '2026-02-28',
                'plazas' => 18,
                'estado' => 'abierto',
            ],
            [
                'titulo' => 'Desarrollo de Apps Móviles',
                'descripcion' => 'Crea aplicaciones móviles multiplataforma desde cero.',
                'fechaInicio' => '2025-12-01',
                'fechaFin' => '2026-03-15',
                'plazas' => 22,
                'estado' => 'abierto',
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::create($curso);
        }
    }
}
