<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorias;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Tecnología', 'descripcion' => 'Noticias sobre avances tecnológicos.'],
            ['nombre' => 'Deportes', 'descripcion' => 'Noticias deportivas y eventos.'],
            ['nombre' => 'Economía', 'descripcion' => 'Información financiera y económica.'],
            ['nombre' => 'Cultura', 'descripcion' => 'Eventos culturales y noticias artísticas.'],
            ['nombre' => 'Salud', 'descripcion' => 'Consejos y noticias sobre salud y bienestar.'],
            ['nombre' => 'Política', 'descripcion' => 'Noticias políticas y análisis.'],
            ['nombre' => 'Educación', 'descripcion' => 'Noticias y novedades educativas.'],
        ];

        foreach ($categorias as $categoria) {
            Categorias::create($categoria);
        }
    }
}
