<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_flujo_completo_de_autenticacion_web()
    {
        // Crear usuario
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Iniciar sesión manualmente
        $this->assertFalse(Auth::check());
        $login = Auth::attempt([
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertTrue($login);
        $this->assertAuthenticatedAs($user);

        // Acceder a una ruta protegida (ajusta la ruta si es necesario)
        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // Cerrar sesión
        Auth::logout();
        $this->assertGuest();

        // Intentar acceder a la ruta protegida tras logout
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login'); // Ajusta si tu ruta de login es diferente
    }
}
