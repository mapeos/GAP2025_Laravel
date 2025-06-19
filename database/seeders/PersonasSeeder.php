<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Persona;

class PersonasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personas = [
            [
                'nombre' => 'Luz',
                'apellido1' => 'Cuesta',
                'apellido2' => 'Mogollon',
                'dni' => '12345678A',
                'tfno' => '600123456',
                'direccion_id' => 1,
                'user_id' => 1,
            ],
            [
                'nombre' => 'Benito',
                'apellido1' => 'Camela',
                'apellido2' => 'Rapido',
                'dni' => '87654321B',
                'tfno' => '600654321',
                'direccion_id' => 2,
                'user_id' => 3,
            ],
        ];

        foreach ($personas as $personaData) {
            Persona::firstOrCreate(
                ['dni' => $personaData['dni']],
                $personaData
            );
        }
        
        $this->command->info('✅ Personas creadas/verificadas.');
    }
}
