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
            'titulo' => 'Descubren una nueva especie en la Amazonía',
            'contenido' => 'Científicos brasileños han identificado una nueva especie de anfibio con características únicas en la región amazónica.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(10),
        ]);

        News::create([
            'titulo' => 'Se inaugura puente colgante más largo del país',
            'contenido' => 'El nuevo puente, que conecta dos regiones rurales, promete reducir los tiempos de traslado y mejorar la conectividad.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(9),
        ]);

        News::create([
            'titulo' => 'Campaña de vacunación supera expectativas',
            'contenido' => 'El Ministerio de Salud anunció que más del 80% de la población objetivo ya recibió su vacuna contra la gripe estacional.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(8),
        ]);

        News::create([
            'titulo' => 'Festival gastronómico reúne a miles de visitantes',
            'contenido' => 'La feria anual de comida regional atrajo a turistas nacionales e internacionales durante todo el fin de semana.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(7),
        ]);

        News::create([
            'titulo' => 'Estudiantes desarrollan robot recolector de basura',
            'contenido' => 'Un grupo de jóvenes presentó un prototipo funcional que busca automatizar la limpieza en espacios públicos.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(6),
        ]);

        News::create([
            'titulo' => 'Crecen exportaciones agrícolas en el último trimestre',
            'contenido' => 'El sector agroexportador reportó un aumento del 15% en comparación con el mismo periodo del año anterior.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(5),
        ]);

        News::create([
            'titulo' => 'Artista local expone sus obras en galería internacional',
            'contenido' => 'La muestra incluye pinturas inspiradas en paisajes latinoamericanos y estará disponible durante todo el mes.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(4),
        ]);

        News::create([
            'titulo' => 'Nuevo sistema de reciclaje entra en funcionamiento',
            'contenido' => 'Las autoridades municipales implementaron un programa piloto para separar residuos en origen en varios distritos.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(3),
        ]);

        News::create([
            'titulo' => 'Estación espacial capta imágenes de tormenta solar',
            'contenido' => 'Los científicos analizan los posibles efectos de la tormenta en las comunicaciones satelitales.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDays(2),
        ]);

        News::create([
            'titulo' => 'Encuentran manuscritos perdidos de escritor famoso',
            'contenido' => 'Los textos inéditos fueron hallados en una biblioteca privada y podrían revelar detalles sobre su obra final.',
            'autor' => rand(1, 5),
            'fecha_publicacion' => Carbon::now()->subDay(),
        ]);
    }
}
