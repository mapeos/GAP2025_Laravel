<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_registrarse_api()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuserapi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);
    }
}
