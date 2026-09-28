# Checklist de evidencias — Campus Connect

## Requisito del enunciado → evidencia

| Requisito | Evidencia en el repo |
|-----------|----------------------|
| Laravel 12 + Blade + PostgreSQL | `backend/`, `docker-compose.yml`, `.env.example` |
| API REST | `backend/routes/api.php` + controladores `Api/` |
| App móvil Flutter (Dev B) | `mobile/` + guía de consumo |
| Repositorio GitHub | https://github.com/SantiagoRoca23/CAMPUS_CONNECT |
| Diseño de casos de uso | `docs/casos-de-uso.md` + `docs/diagramas/*.puml` |
| Arquitectura | `docs/arquitectura.md` |
| ≥ 5 pruebas | `backend/tests/` + CI |
| Integración continua | `.github/workflows/ci.yml` |
| Pruebas antes del merge | Workflow bloqueante en PRs |

## Pruebas mínimas Desarrollador A (Web)

- [x] Inicio de sesión — `AuthTest`
- [x] Crear solicitud — `SolicitudFlujoTest`
- [x] Adjuntar evidencia — `SolicitudFlujoTest`
- [x] Consultar seguimiento — `SolicitudFlujoTest`
- [x] Visualizar comentarios — `SolicitudFlujoTest`

## Funcionalidades Web entregadas

- [x] Dashboard administrativo
- [x] Reportes + export CSV
- [x] Gestión de recursos
- [x] CRUD / atención de solicitudes
- [x] Evidencias y comentarios
- [x] API lista para Flutter

## Capturas sugeridas para el informe

1. Login web
2. Dashboard con métricas
3. Crear solicitud + evidencia
4. Detalle con seguimiento y comentarios
5. Reportes / recursos
6. Resultado de `php artisan test`
7. Diagrama de casos de uso renderizado
8. PR en GitHub con CI en verde
