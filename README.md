# GAP2025_Laravel

> Codigo compartido de la aplicación de gestión de formación para el curso impartido de "Desarrollo de aplicaciones con tecnologías web"

Se parte de una instalación limpia de Laravel 12, starter kit, con Livewire y Volt

# Documento compartido para colaboración

https://docs.google.com/document/d/1gK_PZprqpSuYv6uTT6py5OTcnto8C4YHWykpBa0ruH4/edit?pli=1&tab=t.0

# Funcionalidades

## Parte de backoffice

-   Gestion de usuarios
-   Gestion de comunicaciones
-   -   Push
-   -   Whatsapp
-   -   Mail

# Plantilla

-   Plantilla base: @extends('template.base')
-   Secciones:
-   -   title: Atributo "title" de la página
-   -   title-sidebar: Texto en la parte superior del menu lateral
-   -   title-page: Titulo de la sección en la parte de contenido de la página
-   -   content: Todo el contenido de la página
-   -   breadcrumb: Recorrido de la página actual

-   Inyección de recursos (stack);
-   -   css: en la cabecera de la página. @push('css')
-   -   js: en el pie de la página. @push('js')

## Ejemplo de nueva página

```
@extends('template.base')
@section('title', 'Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Dashboard')
@section('content')
    ...
@endsection
```

Está disponible una página de ejemplo en la ruta: `admin.dashboard.test`

## Ejemplo para 'breadcrumb'

```
@section('breadcrumb')
    <li class="breadcrumb-item "> <a href="#">Forms</a> </li>
    <li class="breadcrumb-item active"> Select Elements </li>
@endsection
```

## Ejemplo de inyección de Javascripc

```
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
```

# Asistente AI para citas en calendario
Ejecutar la migración: `php artisan migrate` 
Se ha creado un comando `php artisan appointments:suggest 1 2 "2025-07-01"` para verificar si se genera una fecha mediate IA

## Motor de IA
Se utiliza un sistema basado en LLM basado en *Ollama + Mistral 7B*
- Ollama es U una plataforma de LLMs que se ejecuta en local
- Mistral 7B es un modelo especializado en instrucciones, entiende cosas como "sugiereme un hueco en la agenda"

Más adelante se podría incluir:
- Phi-2 (Microsoft): modelo pequeño y muy preciso.
- Gemma 2B Instruct (Google): excelente para correr en CPU, más pequeño que Mistral.
 - TinyLlama 1.1B: modelo de 1B de parámetros, ultra ligero.

### Instalación del sistema
- En linux: `curl -fsSL https://ollama.com/install.sh | sh`
- En windows:
```
# Windows (WSL)
wsl --install # (Si no tienes WSL)
wsl
curl -fsSL https://ollama.com/install.sh | sh
```

- cargar el modelo (unos 4.1Gb de espacio): `ollama run mistral`
- exportar variables de sistema (linux):
```
export OLLAMA_HOST=0.0.0.0
export OLLAMA_ORIGINS=*
```
- arrancar *Ollama*: `ollama serve`

Para probar el sistema se puede utilizar un comando como el siguiente:
```
php artisan appointments:suggest-ai 1 2 5 "2025-07-01"   
    --duration=60   
    --tolerance=7   
    --max=3   
    --workingDays='{"monday":["08:00","14:00"],"tuesday":["10:00","18:00"],"wednesday":["08:00","14:00"],"thursday":["08:00","14:00"]}'   
    --excludedDates='["2025-07-04", "2025-07-15"]'   
    --preferences='{"times_of_day":"morning","preferred_days":["tuesday","thursday"],"hour_range":["09:00","11:00"]}'
```

# Agenda/Calendario (Arnaldo y Víctor)

## 1. Introducción
El módulo de Agenda/Calendario permite gestionar y visualizar todos los eventos y actividades del curso. Este sistema está diseñado para facilitar la organización de clases, reuniones, entregas y otros eventos importantes, permitiendo una gestión eficiente del tiempo y los recursos.

---

## 2. Estructura de la Base de Datos
El sistema está compuesto por tres tablas principales que permiten una gestión flexible de eventos:

### Tabla `tipos_evento`
- Define las diferentes categorías de eventos en el calendario
- Campos principales:
  - `nombre`: Tipo de evento (ej: Clase, Entrega, Reunión)
  - `color`: Color para identificación visual en el calendario
  - `status`: Estado activo/inactivo del tipo de evento

### Tabla `eventos`
- Almacena la información de cada evento en el calendario
- Campos principales:
  - `titulo`: Título del evento
  - `descripcion`: Descripción detallada
  - `fecha_inicio`: Fecha y hora de inicio
  - `fecha_fin`: Fecha y hora de finalización
  - `ubicacion`: Lugar del evento (para eventos presenciales)
  - `url_virtual`: Enlace para eventos virtuales
  - `tipo_evento_id`: Categoría del evento
  - `creado_por`: Usuario que crea el evento
  - `status`: Estado activo/inactivo

### Tabla `evento_participante`
- Gestiona la participación de usuarios en los eventos
- Campos principales:
  - `evento_id`: ID del evento
  - `user_id`: ID del participante
  - `rol`: Rol en el evento (ej: Profesor, Alumno, Invitado)
  - `estado_asistencia`: Estado de asistencia
  - `notas`: Notas adicionales
  - `status`: Estado activo/inactivo

---

## 3. Modelos Implementados
Se han desarrollado tres modelos principales que gestionan las relaciones entre eventos y usuarios:

### Modelo `TipoEvento`
- Gestiona las categorías de eventos
- Relación uno a muchos con `Evento`
- Permite filtrar y organizar eventos por tipo

### Modelo `Evento`
- Gestiona la información principal de los eventos
- Relaciones:
  - Pertenece a un `TipoEvento`
  - Pertenece a un `User` (creador)
  - Tiene muchos `User` a través de `EventoParticipante`

### Modelo `EventoParticipante`
- Gestiona la participación en eventos
- Relación muchos a muchos entre `Evento` y `User`
- Permite seguimiento de asistencia y roles

---

## 4. Características Implementadas
- Sistema de categorización de eventos con colores
- Soporte para eventos presenciales y virtuales
- Gestión de participantes y roles
- Seguimiento de asistencia
- Soft deletes para mantener historial
- Timestamps automáticos
- Relaciones Eloquent optimizadas
- Recordatorios personales para alumnos
- API RESTful para aplicación móvil

---

## 5. Rutas y Permisos
### Rutas Web
- **Administradores y Profesores**:
  - Gestión completa de eventos (`/admin/events/*`)
  - Gestión de tipos de evento (`/admin/events/types/*`)
  - Gestión de participantes (`/admin/events/{evento}/participants/*`)

- **Alumnos**:
  - Visualización de calendario (`/events/calendar`)
  - Gestión de recordatorios personales (`/events/reminders/*`)

### Rutas API (App Móvil)
- Endpoints protegidos con Sanctum
- Recursos API para eventos, tipos y participantes
- Autenticación mediante tokens

---

## 6. Próximos Pasos
- Desarrollo de la interfaz de calendario
- Implementación de vistas para:
  - Vista mensual
  - Vista semanal
  - Vista diaria
  - Lista de eventos
- Sistema de notificaciones para eventos
- Filtros por tipo de evento
- Búsqueda de eventos
- Exportación de calendario
- Integración con calendarios externos

## 7. Documentación de la API

### Autenticación
- Todas las rutas requieren autenticación mediante Sanctum
- Token de acceso requerido en el header: `Authorization: Bearer {token}`

### Endpoints Disponibles

#### Eventos
```
GET /api/eventos
- Lista todos los eventos
- Filtros disponibles: fecha_inicio, fecha_fin, tipo_evento_id
- Incluye relaciones: tipoEvento, participantes

GET /api/eventos/{id}
- Detalle de un evento específicp
- Incluye todas las relaciones

POST /api/eventos
- Crea un nuevo evento
- Requiere: titulo, fecha_inicio, fecha_fin, tipo_evento_id
- Opcional: descripcion, ubicacion, url_virtual

PUT /api/eventos/{id}
- Actualiza un evento existente
- Mismos campos que POST

DELETE /api/eventos/{id}
- Elimina un evento (soft delete)
```

#### Tipos de Evento
```
GET /api/tipos-evento
- Lista todos los tipos de evento
- Incluye: id, nombre, color, status

POST /api/tipos-evento
- Crea un nuevo tipo
- Requiere: nombre, color

PUT /api/tipos-evento/{id}
- Actualiza un tipo existente
- Mismos campos que POST

DELETE /api/tipos-evento/{id}
- Elimina un tipo (soft delete)
```

#### Participantes
```
GET /api/evento-participante
- Lista participantes de eventos
- Filtros: evento_id, user_id

POST /api/evento-participante
- Añade un participante
- Requiere: evento_id, user_id, rol

PUT /api/evento-participante/{id}
- Actualiza estado de participante
- Campos: estado_asistencia, notas

DELETE /api/evento-participante/{id}
- Elimina un participante
```

### Formatos de Respuesta

#### Evento
```json
{
    "id": 1,
    "titulo": "Clase de Laravel",
    "descripcion": "Introducción a Laravel",
    "fecha_inicio": "2024-03-20T10:00:00Z",
    "fecha_fin": "2024-03-20T12:00:00Z",
    "ubicacion": "Aula 101",
    "url_virtual": "https://meet.google.com/xxx",
    "tipo_evento": {
        "id": 1,
        "nombre": "Clase",
        "color": "#4CAF50"
    },
    "participantes": [
        {
            "id": 1,
            "nombre": "Juan Pérez",
            "rol": "Profesor",
            "estado_asistencia": "confirmado"
        }
    ],
    "created_at": "2024-03-19T15:00:00Z",
    "updated_at": "2024-03-19T15:00:00Z"
}
```

#### Tipo de Evento
```json
{
    "id": 1,
    "nombre": "Clase",
    "color": "#4CAF50",
    "status": true,
    "created_at": "2024-03-19T15:00:00Z",
    "updated_at": "2024-03-19T15:00:00Z"
}
```

#### Participante
```json
{
    "id": 1,
    "evento_id": 1,
    "user_id": 1,
    "rol": "Profesor",
    "estado_asistencia": "confirmado",
    "notas": "Profesor principal",
    "status": true,
    "created_at": "2024-03-19T15:00:00Z",
    "updated_at": "2024-03-19T15:00:00Z"
}
```

### Código de Estado
- 200: Éxito
- 201: Creado
- 400: Error de validación
- 401: No autenticado
- 403: No autorizado
- 404: No encontrado
- 500: Error del servidor

### Próximos Endpoints a Implementar
- GET /api/eventos/usuario/{id} - Eventos de un usuario específico
- GET /api/eventos/recordatorios - Recordatorios personales
- PUT /api/evento-participante/{id}/asistencia - Actualizar asistencia
- GET /api/eventos/tipo/{id} - Eventos por tipo
- GET /api/eventos/fecha/{fecha} - Eventos por fecha

# Gestión de usuarios y roles (Miguel)

## 1. Introducción
El sistema de gestión de usuarios permite administrar el acceso a las diferentes secciones de la aplicación web, cada usuario visualizará únicamente las funcionalidades correspondientes a su rol: **Administrador**, **Editor**, **Profesor** o **Alumno**. Esta gestión se implementa utilizando el sistema de autenticación y autorización de Laravel.

---

#  Documentación de Gestión de Roles y Permisos (Spatie)

Este documento detalla la instalación, configuración e integración del paquete [`spatie/laravel-permission`](https://spatie.be/docs/laravel-permission) para la gestión de **roles y permisos** en nuestra aplicación de gestión de academia.

---


##  Instalación del paquete

1. Instalar el paquete:

```bash
composer require spatie/laravel-permission
```
2. Publicar archivos de configuración y migraciones:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```
3. Ejecutar las migraciones para crear las tablas necesarias:

```bash
php artisan migrate
```
4. Ejecutar los seeders para crear los usuarios con roles asignados para pruebas 
```bash
php artisan db:seed --class=RolesAndUsersSeeder
```

 Esta migracion:

    - Crea las tablas roles y permissions y sus relaciones 

    - Crea los roles: Administrador, Editor, Profesor, Alumno

    - Crea 4 usuarios con esos roles asignados

| Rol           | Email                                                 | Contraseña |
| ------------- | ----------------------------------------------------- | ---------- |
| Administrador | [admin@academia.com](mailto:admin@academia.com)       | password   |
| Editor        | [editor@academia.com](mailto:editor@academia.com)     | password   |
| Profesor      | [profesor@academia.com](mailto:profesor@academia.com) | password   |
| Alumno        | [alumno@academia.com](mailto:alumno@academia.com)     | password   |

---

# Sanctum para gestionar los usuarios de la APP Movil

Laravel Breeze con Livewire usa autenticación basada en sesión para las vistas web.
Pero para la app móvil, necesitamos tokens, y para eso debemos:

- Habilitar Sanctum como sistema de autenticación solo para la API

- Esto implica:

    Registrar los endpoints /api/login, /api/register, etc.

    Usar auth:sanctum como middleware en las rutas protegidas del API.

## Instalar el paquete de Laravel Sanctum

1. Instalar el paquete:

```bash
composer require laravel/sanctum
```

2. Publicar el archivo de configuracion:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

3. Y ejecuta la migración para crear la tabla `personal_access_tokens`:

```bash
php artisan migrate
```
---

# Gestión de Noticias (Jorge)

## Parte de Backoffice

- **Gestión CRUD de noticias**
  - Posibilidad de crear, editar, eliminar y visualizar noticias.
  - Verificación para evitar la creación de duplicados.
- **Gestión CRUD de categorías**
- **Vinculación de noticias con múltiples categorías** (relación N:N)
- **Vinculación de noticias con usuarios**
  - Solo permitido para usuarios con rol adecuado (_ej. "editor", "admin"_).
- **Borrado lógico para evitar pérdida de datos**
  - Uso de `soft delete` en lugar de eliminación permanente.
  - Protección ante eliminación de categorías con relaciones activas (manejo de errores).

---

## Construcción de Rutas para CRUD de Noticias y Categorías

> Las rutas HTTP se estructuraron de dos formas posibles:

### Opción 1: Rutas manuales

- Declaradas explícitamente una por una.
- Requiere protección futura mediante middleware global o externo.

### Opción 2: Rutas agrupadas y protegidas

- Uso de `Route::prefix()`, `name()` y `middleware()`.
- Crea automáticamente rutas RESTful (`Route::resource()`).
- Protegidas por middlewares como `auth` y `is_admin`.
- Reducción significativa de código repetido.

> **Requiere tener el middleware `is_admin` implementado.**

---

## Ejemplo: Crear el middleware `is_admin`

```bash
php artisan make:middleware IsAdmin
```

-  Ubica y edita el archivo: app/Http/Middleware/IsAdmin.php

```php
public function handle($request, \Closure $next)
{
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }

    abort(403, 'Acceso no autorizado.');
}
```

-   Registrar el middleware Abre el archivo: app/Http/Kernel.php
-   Agrégalo al array de middlewares de ruta ($routeMiddleware):

```php
protected $routeMiddleware = [
    // otros middlewares...
    'is_admin' => \App\Http\Middleware\IsAdmin::class,
];
```

---

# Definir modelos `News` y `Categorias`

-   Para generar los modelos base de Eloquent, ejecuta:

```bash
php artisan make:model News
php artisan make:model Categorias
```

# Crear las Tablas necesarias para la BD

-   Genera las migraciones para las tablas:

```bash
php artisan make:migration create_news_table
php artisan make:migration create_categorias_table
php artisan make:migration create_news_has_categorias_table
```

## Relizar las migraciones una vez creadas las tablas

- Una vez definidas correctamente las estructuras en los archivos de migración, puedes ejecutar las migraciones:

# Dentro de Docker
```bash
docker exec -it alumnos-gap-app php artisan migrate
```

# O directamente si estás trabajando fuera de Docker
```bash
php artisan migrate
```
# Ejecutar los seeders para crear algunas categorias para pruebas 
```bash
php artisan db:seed --class=CategoriaSeeder
```
