<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para crear los roles iniciales.
     */
    public function run(): void
    {
        $roles = ['Administrador', 'Editor', 'Profesor', 'Alumno'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
