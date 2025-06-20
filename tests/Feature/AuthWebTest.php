<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_autenticarse_directamente()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $this->assertFalse(Auth::check());
        $login = Auth::attempt([
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertTrue($login);
        $this->assertAuthenticatedAs($user);
    }

    public function test_usuario_no_puede_autenticarse_con_credenciales_incorrectas()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $login = Auth::attempt([
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);
        $this->assertFalse($login);
        $this->assertGuest();
    }
}
