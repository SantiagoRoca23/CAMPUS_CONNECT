# API REST — Campus Connect

Prefijo: `/api/v1`

Autenticación: `Authorization: Bearer {token}` (excepto login).

## Endpoints

### Auth
- `POST /login`
- `GET /me`
- `POST /logout`

### Solicitudes
- `GET /solicitudes`
- `POST /solicitudes`
- `GET /solicitudes/{id}`
- `PUT|PATCH /solicitudes/{id}`
- `DELETE /solicitudes/{id}`
- `GET /solicitudes/{id}/seguimientos`
- `GET /solicitudes/{id}/comentarios`
- `POST /solicitudes/{id}/comentarios`
- `POST /solicitudes/{id}/evidencias`

### Recursos
- `GET|POST /recursos`
- `GET|PUT|PATCH|DELETE /recursos/{id}`

### Reportes
- `GET /reportes/resumen` (staff)

Ver ejemplos en `mobile/README.md`.
