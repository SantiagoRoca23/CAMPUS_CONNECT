# Campus Connect

Plataforma integrada (Web + API REST + Móvil) para gestionar solicitudes universitarias con trazabilidad completa.

Repositorio: https://github.com/SantiagoRoca23/CAMPUS_CONNECT

## Stack elegido

| Capa | Tecnología |
|------|------------|
| Backend / API | Laravel 12 |
| Base de datos | PostgreSQL |
| Web | Blade |
| Móvil | Flutter (Desarrollador B) |
| Auth API | Laravel Sanctum |
| Pruebas | PHPUnit |
| CI | GitHub Actions |

## Organización del equipo

| Rol | Responsable | Alcance |
|-----|-------------|---------|
| Desarrollador A (Web) | Tú | Frontend Blade, dashboard, reportes, recursos, pruebas web/API |
| Desarrollador B (Móvil) | Compañero | App Flutter, consumo API, registro y seguimiento móvil |
| Compartido | Ambos | BD, API, PRs, integración final |

## Estructura del monorepo

```text
CAMPUS CONNECT/
├── backend/          # Laravel 12 (Web + API)
├── mobile/           # Scaffold Flutter + guía API para Dev B
├── docs/             # Arquitectura, casos de uso, evidencias
├── docker-compose.yml
└── .github/workflows/ci.yml
```

## Arranque rápido (Web + API)

### 1) PostgreSQL

Opción A — Docker:

```bash
docker compose up -d
```

Opción B — Laragon / servicio local PostgreSQL 16+.

Crea la base `campus_connect` (usuario/clave según `.env`).

### 2) Backend

```bash
cd backend
copy .env.example .env
php artisan key:generate
composer install
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Abre: http://127.0.0.1:8000

### Usuarios demo

| Rol | Email | Password |
|-----|-------|----------|
| Administrador | admin@campus.edu | password |
| Administrativo | staff@campus.edu | password |
| Estudiante | estudiante@campus.edu | password |

### Sin PostgreSQL (solo desarrollo local)

En `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

```bash
type nul > database\database.sqlite
php artisan migrate --seed
```

## API para móvil

Documentación completa: [`mobile/README.md`](mobile/README.md)

Base: `http://127.0.0.1:8000/api/v1`

## Pruebas (≥ 5)

```bash
cd backend
php artisan test
```

Cubren:
1. Inicio de sesión web
2. Login API con token
3. Crear solicitud
4. Adjuntar evidencia
5. Consultar seguimiento y comentarios
6. Flujo API crear + cambiar estado
7. Gestión de recursos y reportes
8. Restricción de acceso estudiante → reportes

## CI / Pull Requests

GitHub Actions ejecuta `php artisan test` en cada PR/push.  
**No mergear** si no compila o fallan pruebas.

## Documentación de evidencias

- Arquitectura: [`docs/arquitectura.md`](docs/arquitectura.md)
- Casos de uso (actores con personita UML): [`docs/casos-de-uso.md`](docs/casos-de-uso.md)
- PlantUML: [`docs/diagramas/`](docs/diagramas/)
- Checklist evidencias: [`docs/evidencias/checklist.md`](docs/evidencias/checklist.md)

## Cómo visualizar los diagramas UML

1. Extensión **PlantUML** en VS Code/Cursor, o
2. https://www.plantuml.com/plantuml/uml/ pegando el contenido `.puml`

Los actores se definen con `actor` + `skinparam actorStyle awesome` (símbolo de personita).
