<?php

namespace App\Enums;

enum EstadoSolicitud: string
{
    case Pendiente = 'pendiente';
    case EnProceso = 'en_proceso';
    case EnEspera = 'en_espera';
    case Resuelta = 'resuelta';
    case Cerrada = 'cerrada';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Pendiente => 'Pendiente',
            self::EnProceso => 'En proceso',
            self::EnEspera => 'En espera',
            self::Resuelta => 'Resuelta',
            self::Cerrada => 'Cerrada',
            self::Cancelada => 'Cancelada',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $case) => [$case->value => $case->label()]
        )->all();
    }
}
