<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_flujo_completo_de_autenticacion_api_con_sanctum()
    {
        // Crear usuario
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Login API
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
        $token = $response->json('token');

        // Acceder a endpoint protegido con el token
        $protected = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');
        $protected->assertStatus(200);
        $protected->assertJsonFragment(['email' => $user->email]);

        // Logout API
        $logout = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');
        $logout->assertStatus(200);
    }
}
