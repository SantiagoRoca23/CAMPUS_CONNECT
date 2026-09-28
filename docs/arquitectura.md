# Arquitectura — Campus Connect

## Visión general

Campus Connect es una plataforma integrada para centralizar el ciclo de vida de solicitudes universitarias (registro, evidencia, asignación, seguimiento, cierre y reportes).

```text
┌─────────────────┐     ┌─────────────────┐
│  App Web Blade  │     │  App Móvil      │
│  (Desarrollador │     │  Flutter        │
│   A — Web)      │     │  (Desarrollador │
│                 │     │   B — Móvil)    │
└────────┬────────┘     └────────┬────────┘
         │  Sesión / CSRF        │  Bearer Token
         │                       │  (Laravel Sanctum)
         └───────────┬───────────┘
                     ▼
         ┌───────────────────────┐
         │   Laravel 12 API REST │
         │   + controladores Web │
         └───────────┬───────────┘
                     ▼
         ┌───────────────────────┐
         │     PostgreSQL        │
         └───────────────────────┘
```

## Capas

| Capa | Tecnología | Responsabilidad |
|------|------------|-----------------|
| Presentación Web | Blade + CSS | Dashboard, reportes, recursos, formularios |
| Presentación Móvil | Flutter (pendiente Dev B) | Registro y seguimiento de solicitudes |
| API | Laravel 12 + Sanctum | Contrato JSON compartido |
| Dominio | Modelos Eloquent + Enums | Solicitudes, evidencias, comentarios, recursos |
| Persistencia | PostgreSQL | Fuente de verdad |

## Roles

- **Estudiante**: crea solicitudes, adjunta evidencias, consulta seguimiento y comentarios públicos.
- **Administrativo**: atiende, asigna, cambia estados, gestiona recursos y genera reportes.
- **Administrador**: mismas capacidades de staff con alcance completo.

## Autenticación

- **Web**: sesión Laravel (cookies).
- **API / Móvil**: token personal Sanctum (`Authorization: Bearer …`).

## Flujo de una solicitud

1. Estudiante registra solicitud (+ evidencia opcional).
2. Se crea el primer evento de seguimiento (`pendiente`).
3. Staff asigna responsable → estado `en_proceso`.
4. Comunicación vía comentarios (internos solo staff).
5. Cierre (`resuelta` / `cerrada`) con trazabilidad completa.
6. Reportes consolidan totales, tipología, prioridad y tiempos.
