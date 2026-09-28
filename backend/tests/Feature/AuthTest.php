<?php

namespace Tests\Feature;

use App\Enums\EstadoSolicitud;
use App\Enums\UserRole;
use App\Models\Comentario;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_iniciar_sesion_web(): void
    {
        $user = User::factory()->create([
            'email' => 'estudiante@campus.edu',
            'password' => 'password',
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => 'estudiante@campus.edu',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_api_login_retorna_token(): void
    {
        User::factory()->create([
            'email' => 'api@campus.edu',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'api@campus.edu',
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'email', 'role']]);
    }
}
