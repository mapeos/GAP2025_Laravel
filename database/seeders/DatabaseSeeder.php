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
            RolesAndUsersSeeder::class,
            TipoEventoSeeder::class,
            RolesParticipacionSeeder::class,
            CursosSeeder::class,
            PersonasSeeder::class,
            ParticipacionSeeder::class,
            CategoriaSeeder::class,
            NewsSeeder::class,
        ]);
    }
}
