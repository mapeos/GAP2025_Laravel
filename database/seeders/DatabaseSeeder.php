<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seeders básicos del sistema
            RolesAndUsersSeeder::class,
            RolesSeeder::class,
            
            // Seeders de direcciones (necesarias para personas)
            DireccionesSeeder::class,
            
            // Seeders de eventos
            TipoEventoSeeder::class,
            
            // Seeders relacionados con cursos
            RolesParticipacionSeeder::class,
            CursosSeeder::class,
            PersonasSeeder::class,
            ParticipacionSeeder::class,
            
            // Seeders de contenido
            CategoriaSeeder::class,
            NewsSeeder::class,
            
            // Seeders de citas
            MotivoCitaSeeder::class,
        ]);
    }
}
