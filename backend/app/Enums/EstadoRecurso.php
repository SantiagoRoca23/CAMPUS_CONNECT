<?php

namespace App\Enums;

enum EstadoRecurso: string
{
    case Disponible = 'disponible';
    case EnMantenimiento = 'en_mantenimiento';
    case FueraServicio = 'fuera_servicio';

    public function label(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::EnMantenimiento => 'En mantenimiento',
            self::FueraServicio => 'Fuera de servicio',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->all();
    }
}
