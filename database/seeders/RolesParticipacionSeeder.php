<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RolParticipacion;

class RolesParticipacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador'],
            ['nombre' => 'Profesor'],
            ['nombre' => 'Alumno'],
        ];

        foreach ($roles as $rol) {
            RolParticipacion::firstOrCreate(['nombre' => $rol['nombre']]);
        }
        
        $this->command->info('✅ Roles de participación creados/verificados.');
    }
}
