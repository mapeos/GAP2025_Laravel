<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    public function run()
    {
        $noticias = [
            [
                'titulo' => 'Lanzamiento de la nueva plataforma educativa',
                'contenido' => 'Hoy se ha lanzado la nueva plataforma educativa con múltiples funcionalidades para alumnos y profesores.',
            ],
            [
                'titulo' => 'Inscripciones abiertas para cursos 2025',
                'contenido' => 'Ya puedes inscribirte en los cursos del próximo año académico. Consulta la oferta disponible.',
            ],
            [
                'titulo' => 'Actualización de seguridad completada',
                'contenido' => 'El sistema ha sido actualizado para mejorar la seguridad y el rendimiento.',
            ],
            [
                'titulo' => 'Nuevo curso de Machine Learning',
                'contenido' => 'Se ha añadido un nuevo curso de introducción al Machine Learning. ¡No te lo pierdas!',
            ],
            [
                'titulo' => 'Calendario de exámenes publicado',
                'contenido' => 'Ya está disponible el calendario de exámenes para el semestre actual.',
            ],
            [
                'titulo' => 'Conferencia de expertos en tecnología',
                'contenido' => 'El próximo mes se celebrará una conferencia con expertos invitados del sector tecnológico.',
            ],
            [
                'titulo' => 'Mejoras en la interfaz de usuario',
                'contenido' => 'La plataforma ahora cuenta con una interfaz más intuitiva y moderna.',
            ],
            [
                'titulo' => 'Nueva funcionalidad de mensajería',
                'contenido' => 'Ahora puedes comunicarte directamente con tus profesores y compañeros desde la plataforma.',
            ],
            [
                'titulo' => 'Resultados de las evaluaciones disponibles',
                'contenido' => 'Consulta los resultados de tus evaluaciones en la sección correspondiente.',
            ],
            [
                'titulo' => 'Recordatorio: cierre de inscripciones',
                'contenido' => 'Recuerda que el plazo de inscripción a los cursos finaliza el 15 de septiembre.',
            ],
        ];

        $categoriaIds = DB::table('categorias')->pluck('id')->toArray();
        $adminId = 1; // Cambia este valor si el ID del usuario admin es diferente
        foreach ($noticias as $noticia) {
            $news = News::create([
                'titulo' => $noticia['titulo'],
                'contenido' => $noticia['contenido'],
                'autor' => $adminId, // Debe ser un ID entero válido
                'fecha_publicacion' => Carbon::now(),
            ]);
            if (!empty($categoriaIds)) {
                $news->categorias()->attach(Arr::random($categoriaIds));
            }
        }
    }
}
