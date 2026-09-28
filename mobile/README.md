# Guía para la aplicación móvil (Desarrollador B)

Esta carpeta deja listo el consumo de la **API REST** de Campus Connect. La app debe implementarse en **Flutter**.

## Base URL

```text
http://127.0.0.1:8000/api/v1
```

En dispositivo físico usa la IP de tu PC (ej. `http://192.168.x.x:8000/api/v1`).

## Autenticación (Sanctum)

### Login

`POST /login`

```json
{
  "email": "estudiante@campus.edu",
  "password": "password",
  "device_name": "flutter"
}
```

Respuesta:

```json
{
  "token": "...",
  "token_type": "Bearer",
  "user": { "id": 1, "name": "...", "email": "...", "role": "estudiante" }
}
```

En todas las demás peticiones:

```text
Authorization: Bearer {token}
Accept: application/json
```

### Perfil / Logout

- `GET /me`
- `POST /logout`

## Endpoints de solicitudes (prioridad Dev B)

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/solicitudes` | Listar (estudiante ve las propias) |
| POST | `/solicitudes` | Crear solicitud (`multipart` si hay evidencia) |
| GET | `/solicitudes/{id}` | Detalle + comentarios |
| PUT/PATCH | `/solicitudes/{id}` | Editar |
| DELETE | `/solicitudes/{id}` | Eliminar (solo staff) |
| GET | `/solicitudes/{id}/seguimientos` | Timeline de estados |
| GET | `/solicitudes/{id}/comentarios` | Comentarios visibles |
| POST | `/solicitudes/{id}/comentarios` | Agregar comentario |
| POST | `/solicitudes/{id}/evidencias` | Adjuntar evidencia |

### Crear solicitud (JSON)

```json
{
  "titulo": "Problema de proyector",
  "descripcion": "No enciende en el aula 204",
  "tipo": "equipamiento",
  "prioridad": "alta",
  "ubicacion": "Bloque B · Aula 204"
}
```

Tipos: `mantenimiento`, `soporte_tecnologico`, `infraestructura`, `equipamiento`, `otro`  
Prioridades: `baja`, `media`, `alta`, `critica`  
Estados: `pendiente`, `en_proceso`, `en_espera`, `resuelta`, `cerrada`, `cancelada`

### Adjuntar evidencia

`POST /solicitudes/{id}/evidencias`  
`Content-Type: multipart/form-data`  
Campo archivo: `evidencia`

## Otros endpoints útiles

- `GET /recursos`
- `GET /reportes/resumen` (solo staff)

## Usuarios demo

| Rol | Email | Password |
|-----|-------|----------|
| Estudiante | estudiante@campus.edu | password |
| Administrativo | staff@campus.edu | password |
| Administrador | admin@campus.edu | password |

## Checklist Dev B (Flutter)

- [ ] Pantalla de login con manejo de errores 401/422
- [ ] Listado de solicitudes del usuario
- [ ] Formulario crear/editar solicitud
- [ ] Subida de evidencia
- [ ] Vista de seguimiento (timeline)
- [ ] Vista de comentarios
- [ ] Pruebas mínimas: crear, editar, eliminar, cambiar estado, asignar, reporte (vía API o UI staff)

## Estructura sugerida

```text
mobile/
  lib/
    main.dart
    services/api_client.dart
    models/
    screens/
  README.md   ← este archivo
```

El archivo `lib/services/api_client.dart` ya incluye un cliente base listo para adaptar.
