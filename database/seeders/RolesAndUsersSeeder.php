<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $roles = ['Administrador', 'Editor', 'Profesor', 'Alumno'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Crear usuarios de prueba y asignarles roles
        $usuarios = [
            [
                'name' => 'Admin User',
                'email' => 'admin@academia.com',
                'password' => 'password',
                'role' => 'Administrador'
            ],
            [
                'name' => 'Editor User',
                'email' => 'editor@academia.com',
                'password' => 'password',
                'role' => 'Editor'
            ],
            [
                'name' => 'Profesor User',
                'email' => 'profesor@academia.com',
                'password' => 'password',
                'role' => 'Profesor'
            ],
            [
                'name' => 'Alumno User',
                'email' => 'alumno@academia.com',
                'password' => 'password',
                'role' => 'Alumno'
            ],
        ];

        foreach ($usuarios as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                ]
            );
            $user->assignRole($data['role']);
        }
    }
}
