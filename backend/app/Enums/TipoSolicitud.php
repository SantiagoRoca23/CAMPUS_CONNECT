<?php

namespace App\Enums;

enum TipoSolicitud: string
{
    case Mantenimiento = 'mantenimiento';
    case SoporteTecnologico = 'soporte_tecnologico';
    case Infraestructura = 'infraestructura';
    case Equipamiento = 'equipamiento';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Mantenimiento => 'Mantenimiento',
            self::SoporteTecnologico => 'Soporte tecnológico',
            self::Infraestructura => 'Infraestructura',
            self::Equipamiento => 'Equipamiento',
            self::Otro => 'Otro',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->all();
    }
}
