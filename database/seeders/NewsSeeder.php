<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Illuminate\Support\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        News::create([
            'titulo' => 'Nuevo curso de Laravel disponible',
            'contenido' => 'Aprende a crear aplicaciones web robustas con Laravel, el framework PHP más popular del momento. Incluye proyectos prácticos y acceso a tutores expertos.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(10),
        ]);

        News::create([
            'titulo' => 'Bootcamp de Desarrollo Web Full Stack',
            'contenido' => 'Inscríbete en nuestro bootcamp intensivo y domina HTML, CSS, JavaScript, Vue.js y Node.js. ¡Incluye mentoría personalizada y bolsa de empleo!',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(9),
        ]);

        News::create([
            'titulo' => 'Taller práctico de bases de datos SQL',
            'contenido' => 'Aprende a diseñar, consultar y optimizar bases de datos relacionales con MySQL y PostgreSQL. Ejercicios reales y casos de uso.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(8),
        ]);

        News::create([
            'titulo' => 'Curso de introducción a la Inteligencia Artificial',
            'contenido' => 'Descubre los fundamentos de la IA, machine learning y redes neuronales. Incluye ejemplos en Python y proyectos guiados.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(7),
        ]);

        News::create([
            'titulo' => 'Seminario de seguridad informática para desarrolladores',
            'contenido' => 'Aprende a proteger tus aplicaciones web de ataques comunes y vulnerabilidades. Incluye prácticas de hacking ético.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(6),
        ]);

        News::create([
            'titulo' => 'Workshop de APIs RESTful con Laravel y Vue.js',
            'contenido' => 'Construye y consume APIs modernas, autenticación con tokens y despliegue en la nube. Ejercicios paso a paso.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(5),
        ]);

        News::create([
            'titulo' => 'Curso de Git y control de versiones',
            'contenido' => 'Domina Git, GitHub y los flujos de trabajo colaborativos más usados en la industria. Incluye integración continua.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(4),
        ]);

        News::create([
            'titulo' => 'Nueva edición del curso de React',
            'contenido' => 'Aprende a crear interfaces modernas y reactivas con React. Incluye hooks, context API y despliegue en Vercel.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(3),
        ]);

        News::create([
            'titulo' => 'Masterclass de DevOps y despliegue en la nube',
            'contenido' => 'Automatiza el ciclo de vida de tus aplicaciones con Docker, CI/CD y despliegue en AWS y Azure.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(2),
        ]);

        News::create([
            'titulo' => 'Curso de Python para análisis de datos',
            'contenido' => 'Aprende a procesar, analizar y visualizar datos con Python, Pandas y Matplotlib. Proyectos prácticos incluidos.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDay(),
        ]);
    }
}
