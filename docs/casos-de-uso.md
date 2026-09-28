# Diagramas de casos de uso — Campus Connect

Los diagramas usan notación UML estándar:
- **Actor** = figura de persona (stickman) fuera del sistema.
- **Caso de uso** = óvalo dentro del límite del sistema.
- **Asociación** = línea actor ↔ caso de uso.
- **Include / Extend** = relaciones entre casos de uso.

## 1. Diagrama general del sistema

```plantuml
@startuml CampusConnect_CasosDeUso_General
left to right direction
skinparam actorStyle awesome
skinparam packageStyle rectangle
skinparam shadowing false
skinparam defaultFontName Arial

actor "Estudiante" as Estudiante
actor "Administrativo" as Administrativo
actor "Administrador" as Administrador

rectangle "Campus Connect" {
  usecase "Iniciar sesión" as UC01
  usecase "Crear solicitud" as UC02
  usecase "Adjuntar evidencia" as UC03
  usecase "Consultar seguimiento" as UC04
  usecase "Visualizar comentarios" as UC05
  usecase "Agregar comentario" as UC06
  usecase "Editar solicitud" as UC07
  usecase "Eliminar solicitud" as UC08
  usecase "Cambiar estado" as UC09
  usecase "Asignar responsable" as UC10
  usecase "Gestionar recursos" as UC11
  usecase "Generar reporte" as UC12
  usecase "Ver dashboard" as UC13
}

Estudiante --> UC01
Estudiante --> UC02
Estudiante --> UC03
Estudiante --> UC04
Estudiante --> UC05
Estudiante --> UC06
Estudiante --> UC13

Administrativo --> UC01
Administrativo --> UC04
Administrativo --> UC05
Administrativo --> UC06
Administrativo --> UC07
Administrativo --> UC08
Administrativo --> UC09
Administrativo --> UC10
Administrativo --> UC11
Administrativo --> UC12
Administrativo --> UC13

Administrador --> UC01
Administrador --> UC07
Administrador --> UC08
Administrador --> UC09
Administrador --> UC10
Administrador --> UC11
Administrador --> UC12
Administrador --> UC13

UC02 ..> UC03 : <<extend>>
UC04 ..> UC05 : <<include>>
UC09 ..> UC04 : <<include>>

@enduml
```

## 2. Casos de uso del estudiante (Web / Móvil)

```plantuml
@startuml CampusConnect_Estudiante
left to right direction
skinparam actorStyle awesome
skinparam shadowing false

actor "Estudiante" as E

rectangle "Campus Connect — Módulo Estudiante" {
  usecase "Iniciar sesión" as Login
  usecase "Crear solicitud" as Crear
  usecase "Adjuntar evidencia" as Evidencia
  usecase "Consultar seguimiento" as Seguimiento
  usecase "Visualizar comentarios" as VerComentarios
  usecase "Agregar comentario" as Comentar
}

E --> Login
E --> Crear
E --> Evidencia
E --> Seguimiento
E --> VerComentarios
E --> Comentar

Crear ..> Evidencia : <<extend>>\n(si adjunta archivo)
Seguimiento ..> VerComentarios : <<include>>

@enduml
```

## 3. Casos de uso administrativos (Web)

```plantuml
@startuml CampusConnect_Administrativo
left to right direction
skinparam actorStyle awesome
skinparam shadowing false

actor "Administrativo" as A
actor "Administrador" as Adm

rectangle "Campus Connect — Módulo Administrativo" {
  usecase "Iniciar sesión" as Login
  usecase "Ver dashboard" as Dash
  usecase "Editar solicitud" as Editar
  usecase "Eliminar solicitud" as Eliminar
  usecase "Cambiar estado" as Estado
  usecase "Asignar responsable" as Asignar
  usecase "Gestionar recursos" as Recursos
  usecase "Generar reporte" as Reporte
  usecase "Registrar comentario interno" as Interno
}

A --> Login
A --> Dash
A --> Editar
A --> Eliminar
A --> Estado
A --> Asignar
A --> Recursos
A --> Reporte
A --> Interno

Adm --> Login
Adm --> Dash
Adm --> Editar
Adm --> Eliminar
Adm --> Estado
Adm --> Asignar
Adm --> Recursos
Adm --> Reporte

Estado ..> Asignar : <<extend>>
Reporte ..> Dash : <<include>>

@enduml
```

## 4. Especificación breve de actores

| Actor | Tipo | Descripción |
|-------|------|-------------|
| Estudiante | Primario | Persona usuaria que reporta y consulta solicitudes. |
| Administrativo | Primario | Personal que atiende, prioriza y da seguimiento. |
| Administrador | Primario | Supervisa operaciones, recursos y reportes. |

> Nota UML: el **actor** siempre se representa con el símbolo de personita (stickman). No se usa un rectángulo ni un caso de uso para representar personas. El **sistema** se delimita con un rectángulo que contiene solo los óvalos de casos de uso.
