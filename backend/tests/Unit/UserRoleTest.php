<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_roles_staff_se_identifican_correctamente(): void
    {
        $this->assertTrue(UserRole::Administrativo->isStaff());
        $this->assertTrue(UserRole::Administrador->isStaff());
        $this->assertFalse(UserRole::Estudiante->isStaff());
    }
}
