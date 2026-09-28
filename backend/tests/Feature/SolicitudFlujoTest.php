<?php

namespace Tests\Feature;

use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use App\Enums\UserRole;
use App\Models\Comentario;
use App\Models\Seguimiento;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SolicitudFlujoTest extends TestCase
{
    use RefreshDatabase;

    public function test_estudiante_puede_crear_solicitud(): void
    {
        $estudiante = User::factory()->create(['role' => UserRole::Estudiante]);

        $response = $this->actingAs($estudiante)->post(route('solicitudes.store'), [
            'titulo' => 'Falla en red WiFi del bloque C',
            'descripcion' => 'No hay conectividad estable en el laboratorio de redes desde esta mañana.',
            'tipo' => TipoSolicitud::SoporteTecnologico->value,
            'prioridad' => PrioridadSolicitud::Alta->value,
            'ubicacion' => 'Bloque C · Lab 3',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('solicitudes', [
            'titulo' => 'Falla en red WiFi del bloque C',
            'user_id' => $estudiante->id,
            'estado' => EstadoSolicitud::Pendiente->value,
        ]);
        $this->assertDatabaseCount('seguimientos', 1);
    }

    public function test_estudiante_puede_adjuntar_evidencia(): void
    {
        Storage::fake('public');

        $estudiante = User::factory()->create(['role' => UserRole::Estudiante]);
        $solicitud = Solicitud::factory()->create(['user_id' => $estudiante->id]);

        $response = $this->actingAs($estudiante)->post(route('solicitudes.evidencias', $solicitud), [
            'evidencia' => UploadedFile::fake()->image('evidencia.jpg'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('evidencias', 1);
    }

    public function test_estudiante_consulta_seguimiento_y_comentarios(): void
    {
        $estudiante = User::factory()->create(['role' => UserRole::Estudiante]);
        $staff = User::factory()->administrativo()->create();

        $solicitud = Solicitud::factory()->create(['user_id' => $estudiante->id]);

        Seguimiento::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $staff->id,
            'estado_anterior' => EstadoSolicitud::Pendiente,
            'estado_nuevo' => EstadoSolicitud::EnProceso,
            'nota' => 'En atención',
        ]);

        Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $staff->id,
            'contenido' => 'Estamos revisando el caso.',
            'es_interno' => false,
        ]);

        Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $staff->id,
            'contenido' => 'Nota interna',
            'es_interno' => true,
        ]);

        $response = $this->actingAs($estudiante)->get(route('solicitudes.show', $solicitud));

        $response->assertOk()
            ->assertSee('En atención')
            ->assertSee('Estamos revisando el caso.')
            ->assertDontSee('Nota interna');
    }

    public function test_api_crear_solicitud_y_cambiar_estado(): void
    {
        $estudiante = User::factory()->create(['role' => UserRole::Estudiante]);
        $staff = User::factory()->administrativo()->create();

        $create = $this->actingAs($estudiante, 'sanctum')->postJson('/api/v1/solicitudes', [
            'titulo' => 'Silla dañada en biblioteca',
            'descripcion' => 'Una silla del segundo piso está rota y representa riesgo.',
            'tipo' => TipoSolicitud::Infraestructura->value,
            'prioridad' => PrioridadSolicitud::Media->value,
        ]);

        $create->assertCreated();
        $id = $create->json('data.id');

        $update = $this->actingAs($staff, 'sanctum')->patchJson('/api/v1/solicitudes/'.$id, [
            'estado' => EstadoSolicitud::EnProceso->value,
            'asignado_a' => $staff->id,
            'nota' => 'Tomada por infraestructura',
        ]);

        $update->assertOk()
            ->assertJsonPath('data.estado', EstadoSolicitud::EnProceso->value);

        $this->assertDatabaseHas('seguimientos', [
            'solicitud_id' => $id,
            'estado_nuevo' => EstadoSolicitud::EnProceso->value,
        ]);
    }
}
