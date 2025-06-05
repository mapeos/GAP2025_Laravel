<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoEvento;

class TipoEventoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
        [    'nombre' => 'Recordatorio Personal',
            'color' => '#FFA500', // Naranja
            'status' => true,
        ],
        [
            'nombre' => 'Clase',
            'color' => '#4CAF50', // Verde
            'status' => true,
        ],
        [
            'nombre' => 'Entrega',
            'color' => '#F44336', // Rojo
            'status' => true,
        ],
        [
            'nombre' => 'Reunión',
            'color' => '#2196F3', // Azul
            'status' => true,
        ],
    ];

        foreach ($tipos as $tipo) {
            TipoEvento::create($tipo);
        }
    }
}