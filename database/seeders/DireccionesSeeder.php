<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Direccion;

class DireccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $direcciones = [
            [
                'calle' => 'Avenida de Viveiro',
                'numero' => '1',
                'piso' => '2A',
                'ciudad' => 'Lugo',
                'provincia' => 'Lugo',
                'cp' => '27002',
                'pais' => 'España',
            ],
            [
                'calle' => 'Calle Pontevedra',
                'numero' => '5',
                'piso' => 'Bajo',
                'ciudad' => 'Lalin',
                'provincia' => 'Pontevedra',
                'cp' => '36500',
                'pais' => 'España',
            ],
        ];

        foreach ($direcciones as $direccion) {
            Direccion::firstOrCreate(
                [
                    'calle' => $direccion['calle'],
                    'numero' => $direccion['numero'],
                    'ciudad' => $direccion['ciudad']
                ],
                $direccion
            );
        }
        
        $this->command->info('✅ Direcciones creadas/verificadas.');
    }
}
