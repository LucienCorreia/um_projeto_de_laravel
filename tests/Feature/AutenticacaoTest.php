<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AutenticacaoTest extends TestCase
{

    use RefreshDatabase;

    public function test_login_valido(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this->post('/v1/auth/token', [
            'email' => $usuario->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    public function test_senha_errada(): void
    {
        $usuario = Usuario::factory()->create();

        $response = $this->post('/v1/auth/token', [
            'email' => $usuario->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized'
        ]);
    }
}
