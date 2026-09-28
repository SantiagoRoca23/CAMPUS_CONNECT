<?php

namespace Database\Seeders;

use App\Enums\EstadoRecurso;
use App\Enums\EstadoSolicitud;
use App\Enums\PrioridadSolicitud;
use App\Enums\TipoSolicitud;
use App\Enums\UserRole;
use App\Models\Comentario;
use App\Models\Recurso;
use App\Models\Seguimiento;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Campus',
            'email' => 'admin@campus.edu',
            'password' => Hash::make('password'),
            'role' => UserRole::Administrador,
            'codigo_institucional' => 'ADM-001',
            'facultad' => 'Rectoría',
            'telefono' => '3001112233',
        ]);

        $staff = User::create([
            'name' => 'Laura Soporte',
            'email' => 'staff@campus.edu',
            'password' => Hash::make('password'),
            'role' => UserRole::Administrativo,
            'codigo_institucional' => 'STAFF-014',
            'facultad' => 'TI / Infraestructura',
            'telefono' => '3002223344',
        ]);

        $estudiante = User::create([
            'name' => 'Santiago Estudiante',
            'email' => 'estudiante@campus.edu',
            'password' => Hash::make('password'),
            'role' => UserRole::Estudiante,
            'codigo_institucional' => 'EST-2024001',
            'facultad' => 'Ingeniería',
            'telefono' => '3003334455',
        ]);

        $proyector = Recurso::create([
            'codigo' => 'REC-PRJ-01',
            'nombre' => 'Proyector Aula 204',
            'tipo' => 'Equipamiento',
            'ubicacion' => 'Bloque B · Aula 204',
            'estado' => EstadoRecurso::EnMantenimiento,
            'descripcion' => 'Equipo de proyección con falla intermitente.',
        ]);

        Recurso::create([
            'codigo' => 'REC-LAB-03',
            'nombre' => 'Laboratorio de redes',
            'tipo' => 'Infraestructura',
            'ubicacion' => 'Bloque C · Lab 3',
            'estado' => EstadoRecurso::Disponible,
            'descripcion' => 'Sala con 24 puestos.',
        ]);

        $solicitud = Solicitud::create([
            'codigo' => Solicitud::generarCodigo(),
            'titulo' => 'Proyector no enciende en aula 204',
            'descripcion' => 'El proyector del aula 204 no responde al control ni al encendido manual. Se necesita revisión urgente para clase de mañana.',
            'tipo' => TipoSolicitud::Equipamiento,
            'prioridad' => PrioridadSolicitud::Alta,
            'estado' => EstadoSolicitud::EnProceso,
            'ubicacion' => 'Bloque B · Aula 204',
            'user_id' => $estudiante->id,
            'asignado_a' => $staff->id,
            'recurso_id' => $proyector->id,
        ]);

        Seguimiento::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $estudiante->id,
            'estado_anterior' => null,
            'estado_nuevo' => EstadoSolicitud::Pendiente,
            'nota' => 'Solicitud registrada por el estudiante.',
        ]);

        Seguimiento::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $admin->id,
            'estado_anterior' => EstadoSolicitud::Pendiente,
            'estado_nuevo' => EstadoSolicitud::EnProceso,
            'nota' => 'Asignada a soporte de infraestructura.',
        ]);

        Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $staff->id,
            'contenido' => 'Recibimos la solicitud. Revisaremos el cableado y la lámpara del equipo.',
            'es_interno' => false,
        ]);

        Comentario::create([
            'solicitud_id' => $solicitud->id,
            'user_id' => $staff->id,
            'contenido' => 'Pedido interno de repuesto en trámite.',
            'es_interno' => true,
        ]);

        Solicitud::create([
            'codigo' => 'SC-'.now()->format('Y').'-00002',
            'titulo' => 'Fuga menor en baños del bloque A',
            'descripcion' => 'Se observa humedad constante cerca de los lavamanos del primer piso.',
            'tipo' => TipoSolicitud::Mantenimiento,
            'prioridad' => PrioridadSolicitud::Media,
            'estado' => EstadoSolicitud::Pendiente,
            'ubicacion' => 'Bloque A · Baños 1P',
            'user_id' => $estudiante->id,
        ]);
    }
}
