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
        for ($i = 1; $i <= 10; $i++) {
            News::create([
                'titulo' => "Noticia de prueba número $i",
                'contenido' => "Este es el contenido de la noticia de prueba número $i. Aquí iría una descripción más extensa del evento o situación.",
                'autor' => rand(1, 5), // Simulando IDs de usuarios del 1 al 5
                'fecha_publicacion' => Carbon::now()->subDays(10 - $i), // Publicadas en días recientes
            ]);
        }
    }
}
