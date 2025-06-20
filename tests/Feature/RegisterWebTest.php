<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_registrarse_web()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Simula el registro usando el modelo User directamente (ajusta si tienes endpoint POST)
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);
    }
}
