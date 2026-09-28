<?php

namespace Database\Factories;

use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Solicitud>
 */
class SolicitudFactory extends Factory
{
    protected $model = Solicitud::class;

    public function definition(): array
    {
        return [
            'codigo' => 'SC-'.now()->format('Y').'-'.fake()->unique()->numerify('#####'),
            'titulo' => fake()->sentence(4),
            'descripcion' => fake()->paragraph(),
            'tipo' => fake()->randomElement(TipoSolicitud::cases()),
            'prioridad' => fake()->randomElement(PrioridadSolicitud::cases()),
            'estado' => EstadoSolicitud::Pendiente,
            'ubicacion' => 'Bloque '.fake()->randomLetter().' · Aula '.fake()->numberBetween(100, 320),
            'user_id' => User::factory(),
        ];
    }
}
