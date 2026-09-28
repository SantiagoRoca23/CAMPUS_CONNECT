<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Recurso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecursoReporteTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrativo_gestiona_recursos_y_ve_reportes(): void
    {
        $staff = User::factory()->administrativo()->create();

        $create = $this->actingAs($staff)->post(route('recursos.store'), [
            'codigo' => 'REC-TEST-01',
            'nombre' => 'Switch de red edificio D',
            'tipo' => 'Equipamiento',
            'ubicacion' => 'Bloque D · Cuarto técnico',
            'estado' => 'disponible',
            'descripcion' => 'Equipo de red principal',
        ]);

        $create->assertRedirect(route('recursos.index'));
        $this->assertDatabaseHas('recursos', ['codigo' => 'REC-TEST-01']);

        $reporte = $this->actingAs($staff)->get(route('reportes.index'));
        $reporte->assertOk()->assertSee('Reportes consolidados');
    }

    public function test_estudiante_no_accede_a_reportes(): void
    {
        $estudiante = User::factory()->create(['role' => UserRole::Estudiante]);

        $this->actingAs($estudiante)
            ->get(route('reportes.index'))
            ->assertForbidden();
    }
}
