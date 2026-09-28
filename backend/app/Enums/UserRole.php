<?php

namespace App\Enums;

enum UserRole: string
{
    case Estudiante = 'estudiante';
    case Administrativo = 'administrativo';
    case Administrador = 'administrador';

    public function label(): string
    {
        return match ($this) {
            self::Estudiante => 'Estudiante',
            self::Administrativo => 'Administrativo',
            self::Administrador => 'Administrador',
        };
    }

    public function isStaff(): bool
    {
        return $this === self::Administrativo || $this === self::Administrador;
    }
}
