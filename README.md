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

-   Define las diferentes categorías de eventos en el calendario
-   Campos principales:
    -   `nombre`: Tipo de evento (ej: Clase, Entrega, Reunión)
    -   `color`: Color para identificación visual en el calendario
    -   `status`: Estado activo/inactivo del tipo de evento

### Tabla `eventos`

-   Almacena la información de cada evento en el calendario
-   Campos principales:
    -   `titulo`: Título del evento
    -   `descripcion`: Descripción detallada
    -   `fecha_inicio`: Fecha y hora de inicio
    -   `fecha_fin`: Fecha y hora de finalización
    -   `ubicacion`: Lugar del evento (para eventos presenciales)
    -   `url_virtual`: Enlace para eventos virtuales
    -   `tipo_evento_id`: Categoría del evento
    -   `creado_por`: Usuario que crea el evento
    -   `status`: Estado activo/inactivo

### Tabla `evento_participante`

-   Gestiona la participación de usuarios en los eventos
-   Campos principales:
    -   `evento_id`: ID del evento
    -   `user_id`: ID del participante
    -   `rol`: Rol en el evento (ej: Profesor, Alumno, Invitado)
    -   `estado_asistencia`: Estado de asistencia
    -   `notas`: Notas adicionales
    -   `status`: Estado activo/inactivo

---

## 3. Modelos Implementados

Se han desarrollado tres modelos principales que gestionan las relaciones entre eventos y usuarios:

### Modelo `TipoEvento`

-   Gestiona las categorías de eventos
-   Relación uno a muchos con `Evento`
-   Permite filtrar y organizar eventos por tipo

### Modelo `Evento`

-   Gestiona la información principal de los eventos
-   Relaciones:
    -   Pertenece a un `TipoEvento`
    -   Pertenece a un `User` (creador)
    -   Tiene muchos `User` a través de `EventoParticipante`

### Modelo `EventoParticipante`

-   Gestiona la participación en eventos
-   Relación muchos a muchos entre `Evento` y `User`
-   Permite seguimiento de asistencia y roles

---

## 4. Características Implementadas

-   Sistema de categorización de eventos con colores
-   Soporte para eventos presenciales y virtuales
-   Gestión de participantes y roles
-   Seguimiento de asistencia
-   Soft deletes para mantener historial
-   Timestamps automáticos
-   Relaciones Eloquent optimizadas
-   Recordatorios personales para alumnos
-   API RESTful para aplicación móvil

---

## 5. Rutas y Permisos

### Rutas Web

-   **Administradores y Profesores**:

    -   Gestión completa de eventos (`/admin/events/*`)
    -   Gestión de tipos de evento (`/admin/events/types/*`)
    -   Gestión de participantes (`/admin/events/{evento}/participants/*`)

-   **Alumnos**:
    -   Visualización de calendario (`/events/calendar`)
    -   Gestión de recordatorios personales (`/events/reminders/*`)

### Rutas API (App Móvil)

-   Endpoints protegidos con Sanctum
-   Recursos API para eventos, tipos y participantes
-   Autenticación mediante tokens

---

## 6. Próximos Pasos

-   Desarrollo de la interfaz de calendario
-   Implementación de vistas para:

    -   Lista de eventos

-   Sistema de notificaciones para eventos
-   Filtros por tipo de evento
-   Búsqueda de eventos
-   Exportación de calendario
-   Integración con calendarios externos

## 7. Documentación de la API

### Autenticación

-   Todas las rutas requieren autenticación mediante Sanctum
-   Token de acceso requerido en el header: `Authorization: Bearer {token}`

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

-   200: Éxito
-   201: Creado
-   400: Error de validación
-   401: No autenticado
-   403: No autorizado
-   404: No encontrado
-   500: Error del servidor

### Próximos Endpoints a Implementar

-   GET /api/eventos/usuario/{id} - Eventos de un usuario específico
-   GET /api/eventos/recordatorios - Recordatorios personales
-   PUT /api/evento-participante/{id}/asistencia - Actualizar asistencia
-   GET /api/eventos/tipo/{id} - Eventos por tipo
-   GET /api/eventos/fecha/{fecha} - Eventos por fecha

# Gestión de usuarios y roles (Miguel)

## 1. Introducción

El sistema de gestión de usuarios permite administrar el acceso a las diferentes secciones de la aplicación web, cada usuario visualizará únicamente las funcionalidades correspondientes a su rol: **Administrador**, **Editor**, **Profesor** o **Alumno**. Esta gestión se implementa utilizando el sistema de autenticación y autorización de Laravel.

---

# Documentación de Gestión de Roles y Permisos (Spatie)

Este documento detalla la instalación, configuración e integración del paquete [`spatie/laravel-permission`](https://spatie.be/docs/laravel-permission) para la gestión de **roles y permisos** en nuestra aplicación de gestión de academia.

---

## Instalación del paquete

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

4. Ejecución de Seeders

Para poblar la base de datos con datos de prueba, puedes ejecutar los siguientes comandos:

## Ejecutar todos los seeders en orden

```bash
php artisan db:seed
```

Este comando ejecutará todos los seeders en el siguiente orden:

1. DatabaseSeeder (base)
2. RolesAndUsersSeeder (roles y usuarios de prueba)
3. TipoEventoSeeder (categorías de eventos)
4. EventoSeeder (eventos de ejemplo)
5. EventoParticipanteSeeder (asignación de participantes)
6. CategoriaSeeder (categorías de noticias)

## Ejecutar seeders específicos

Si necesitas ejecutar solo ciertos seeders, puedes usar:

```bash
php artisan db:seed --class=NombreDelSeeder
```

Por ejemplo:

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

-   Habilitar Sanctum como sistema de autenticación solo para la API

-   Esto implica:

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

# Flujo de autenticación y registro para la app móvil

Este es el flujo completo para la autenticación y registro de dispositivos móviles en el backend:

---

### 1. Solicitud desde la app móvil

-   El usuario se registra o inicia sesión desde la app móvil enviando sus credenciales (y opcionalmente datos del dispositivo) a:
    -   POST `/api/auth/register` o `/api/auth/login`

#### Datos enviados:

```json
{
    "name": "Nombre", // solo en registro
    "email": "usuario@dom.com",
    "password": "secreto",
    "device_id": "uuid-dispositivo",
    "device_name": "iPhone 15",
    "device_os": "iOS 17",
    "device_token": "token_push",
    "app_version": "1.0.0",
    "extra_data": { "foo": "bar" }
}
```

---

### 2. Backend procesa la solicitud

-   Valida credenciales.
-   Si son correctas:
    -   Crea o actualiza el usuario.
    -   Crea o actualiza el registro del dispositivo en la tabla `devices` (asociado al usuario).
    -   Genera un token de acceso (Sanctum) para el usuario.

---

### 3. Respuesta del backend

-   Devuelve un JSON con:
    -   Los datos del usuario.
    -   El token de acceso para autenticación API.
    -   (Opcional) Los datos del dispositivo.

```json
{
  "user": { ... },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
  "device": { ... } // si lo implementas así
}
```

---

### 4. Uso del token en la app móvil

-   La app almacena el token recibido.
-   Para futuras peticiones protegidas, la app envía el token en la cabecera:
    ```
    Authorization: Bearer <token>
    ```

---

### 5. Guardar/actualizar info del dispositivo

-   Si la app quiere actualizar la info del dispositivo, puede hacer un POST a:
    -   `/api/auth/device` (con el token en la cabecera)
-   El backend actualiza o crea el registro en la tabla `devices`.

---

### 6. Otros equipos pueden usar la tabla `api_tokens`

-   Si otro equipo necesita gestionar tokens independientes, puede usar la tabla `api_tokens` para almacenar y consultar tokens asociados a usuarios y/o dispositivos.

---

# Gestión de Noticias (Jorge)

## Parte de Backoffice

### Funcionalidades Implementadas

-   **Gestión CRUD de noticias**
    -   Crear, editar, eliminar y visualizar noticias
    -   Verificación para evitar la creación de duplicados
    -   Soporte para imágenes en formatos JPG y PNG (máximo 2MB)
    -   Sistema de borrado lógico (soft delete)
    -   Paginación de resultados

-   **Gestión CRUD de categorías**
    -   Crear, editar, eliminar y visualizar categorías
    -   Protección ante eliminación de categorías con noticias asociadas
    -   Sistema de borrado lógico (soft delete)

-   **Relaciones**
    -   Vinculación de noticias con múltiples categorías (relación N:N)
    -   Vinculación de noticias con usuarios (autor)
    -   Control de acceso basado en roles (editor, admin)

### Configuración Inicial

> Las rutas HTTP se estructuraron de dos formas posibles:

### Opción 1: Rutas manuales

-   Declaradas explícitamente una por una.
-   Requiere protección futura mediante middleware global o externo.

### Opción 2: Rutas agrupadas y protegidas

-   Uso de `Route::prefix()`, `name()` y `middleware()`.
-   Crea automáticamente rutas RESTful (`Route::resource()`).
-   Protegidas por middlewares como `auth` y `is_admin`.
-   Reducción significativa de código repetido.

> **Requiere tener el middleware `is_admin` implementado.**

---

## Ejemplo: Crear el middleware `is_admin`

```bash
php artisan make:middleware IsAdmin
```

-   Ubica y edita el archivo: app/Http/Middleware/IsAdmin.php

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

2. **Crear las migraciones**
```bash
php artisan make:migration create_news_table
php artisan make:migration create_categorias_table
php artisan make:migration create_news_has_categorias_table
```

3. **Ejecutar las migraciones**
```bash
# Dentro de Docker
docker exec -it alumnos-gap-app php artisan migrate

# O directamente
php artisan migrate
```

4. **Cargar datos de prueba**
```bash
# Crear categorías de prueba
php artisan db:seed --class=CategoriaSeeder

# Crear noticias de prueba
php artisan db:seed --class=NewsSeeder
```

### Estructura de Directorios para Imágenes

Este comando creará un enlace simbólico en `public/storage` que apunta a `storage/app/public`. Las imágenes de las noticias se almacenarán en `storage/app/public/news` y serán accesibles públicamente a través de la URL `/storage/news/nombre-del-archivo`.

5. **Configurar el almacenamiento de imágenes**
```bash
# Dentro de Docker
docker exec -it alumnos-gap-app php artisan storage:link

# O directamente
php artisan storage:link
```

### Estructura de Rutas

Las rutas están implementadas de dos formas:

1. **Rutas Agrupadas (Recomendada)**
```php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {
    Route::resource('news', NewsController::class);
    Route::resource('categorias', CategoriasController::class);
});
```

2. **Rutas Manuales**
```php
Route::get('/admin/news', [NewsController::class, 'index'])->name('admin.news.index');
Route::post('/admin/news', [NewsController::class, 'store'])->name('admin.news.store');
// ... más rutas
```

### Middleware de Control de Acceso (en caso de necesitarlo!)

Para proteger las rutas administrativas:

1. **Crear el middleware**
```bash
php artisan make:middleware IsAdmin
```

2. **Implementar la lógica**
```php
public function handle($request, \Closure $next)
{
    if (auth()->check() && auth()->user()->hasRole(['admin', 'editor'])) {
        return $next($request);
    }
    abort(403, 'Acceso no autorizado.');
}
```

3. **Registrar el middleware**
```php
protected $routeMiddleware = [
    'is_admin' => \App\Http\Middleware\IsAdmin::class,
];
```
---

# API

# Documentación de la API de Noticias:
# API de Cursos

### 1. Listar todas las noticias  
**GET** `/api/news`  
Descripción: Devuelve todas las noticias con su categoría.  
Parámetros opcionales (query):  
- `q` (string): Buscar por palabra clave en el título o contenido.  
- `category` (string): Nombre de la categoría para filtrar.  
- `per_page` (int): Cantidad de resultados por página (por defecto: 10).  

**Respuesta exitosa:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "titulo": "Título de la noticia",
      "contenido": "Texto de la noticia",
      "fecha_publicacion": "2024-06-04",
      "categorias": [
        {
          "id": 1,
          "nombre": "General",
          "descripcion": "Noticias generales"
        }
      ]
    }
  ],
  "total": 20,
  "per_page": 10,
  "last_page": 2
}
```

### 2. Obtener noticia por ID  
**GET** `/api/news/{id}`  
Parámetro de ruta:  
- `id` (integer): ID de la noticia.

**Respuesta exitosa:**
```json
{
  "id": 1,
  "titulo": "Título de la noticia",
  "contenido": "Texto de la noticia",
  "fecha_publicacion": "2024-06-04",
  "categorias": [
    {
      "id": 1,
      "nombre": "General",
      "descripcion": "Noticias generales"
    }
  ]
}
```

**Respuesta si no existe:**  
Código HTTP: 404  
```json
{
  "message": "No query results for model [App\\Models\\News] 999"
}
```

### 3. Listar noticias por categoría  
**GET** `/api/news?category={nombre}`  
Parámetro de query:  
- `category` (string): Nombre de la categoría.

**Respuesta exitosa:**
```json
{
  "status": "200",
  "data": [
    {
      "id": 2,
      "titulo": "Otra noticia",
      "contenido": "Texto...",
      "fecha_publicacion": "2024-06-04",
      "categorias": [
        {
          "id": 2,
          "nombre": "Deportes",
          "descripcion": "Noticias deportivas"
        }
      ]
    }
  ]
}
```

**Respuesta si no hay noticias:**  
Código HTTP: 404  
```json
{
  "status": "error",
  "message": "No news found for this category"
}
```

### 4. Últimas noticias  
**GET** `/api/news/latest/{number?}`  
Descripción: Devuelve las noticias más recientes.  
Parámetro opcional:  
- `number` (integer): Número de noticias a devolver (por defecto 5).

**Respuesta exitosa:**
```json
{
  "status": "200",
  "data": [
    {
      "id": 5,
      "titulo": "Noticia reciente",
      "contenido": "Texto...",
      "fecha_publicacion": "2024-06-04",
      "categorias": [
        {
          "id": 1,
          "nombre": "General",
          "descripcion": "Noticias generales"
        }
      ]
    }
  ]
}
```

### 5. Listar todas las categorías  
**GET** `/api/categorias`  
Descripción: Devuelve todas las categorías disponibles.

**Respuesta exitosa:**
```json
{
  "status": "200",
  "data": [
    {
      "id": 1,
      "nombre": "General",
      "descripcion": "Noticias generales"
    },
    {
      "id": 2,
      "nombre": "Deportes",
      "descripcion": "Noticias deportivas"
    }
  ]
}
```

# Documentación de la API de Cursos:
# API de Cursos

### 1. Listar todos los cursos
- Método: GET
- URL: /api/cursos
- Descripción: Devuelve todos los cursos.
- Respuesta exitosa:
```json
    {
      "status": "200",
      "message": "Cursos obtenidos correctamente",
      "data": [
        {
          "id": 1,
          "titulo": "Curso de Laravel Básico",
          "descripcion": "Aprende los fundamentos de Laravel.",
          "fechaInicio": "2025-07-01",
          "fechaFin": "2025-07-15",
          "plazas": 30,
          "estado": "activo",
          "created_at": "...",
          "updated_at": "..."
        }
      ]
    }
```

### 2. Obtener un curso por ID
- Método: GET
- URL: /api/cursos/{id}
- Parámetros:
  - id (integer): ID del curso
- Descripción: Devuelve un curso específico.
- Respuesta exitosa:
```json
    {
      "status": "200",
      "message": "Curso obtenido correctamente",
      "data": {
        "id": 1,
        "titulo": "Curso de Laravel Básico",
        "descripcion": "Aprende los fundamentos de Laravel.",
        "fechaInicio": "2025-07-01",
        "fechaFin": "2025-07-15",
        "plazas": 30,
        "estado": "activo",
        "created_at": "...",
        "updated_at": "..."
      }
    }
- Respuesta si no existe:
    {
      "status": "error",
      "message": "Curso no encontrado"
    }
HTTP Status: 404
```

### 3. Listar cursos activos
- Método: GET
- URL: /api/cursos/activos
- Descripción: Devuelve todos los cursos con estado "activo".
- Respuesta exitosa:
```json
    {
      "status": "200",
      "message": "Cursos activos obtenidos correctamente",
      "data": [ /* cursos activos */ ]
    }
    ```

### 4. Listar cursos inactivos
- Método: GET
- URL: /api/cursos/inactivos
- Descripción: Devuelve todos los cursos con estado "inactivo".
- Respuesta exitosa:
```json
    {
      "status": "200",
      "message": "Cursos inactivos obtenidos correctamente",
      "data": [ /* cursos inactivos */ ]
    }
    ```
### 5.Listar cursos ordenados por fecha de inicio descendente
- Método: GET
- URL: /api/cursos/ordenados-por-fecha-inicio-desc
- Descripción: Devuelve cursos ordenados por fechaInicio descendente.
- Respuesta exitosa:
```json
    {
      "status": "200",
      "message": "Cursos ordenados por fecha de inicio descendente obtenidos correctamente",
      "data": [ /* cursos ordenados */ ]
    }
    ```
### 6. Obtener los últimos cursos
- Método: GET
- URL: /api/cursos/ultimos/{number?}
- Parámetros opcionales:
- number (integer): Número de cursos a devolver (por defecto 5)
- Descripción: Devuelve los últimos cursos creados, limitados a number.
- Respuesta exitosa:
```json
{
  "status": "200",
  "message": "Últimos cursos obtenidos correctamente",
  "data": [ /* últimos cursos */ ]
}
```
### 7. Buscar y filtrar cursos
- Método: GET
- URL: /api/cursos/buscar-filtrar
Parámetros query opcionales:
- search (string): Texto para buscar en título o descripción.
- estado (string): Filtrar por estado ('activo' o 'inactivo').
- orden (string): Ordenar por fechaInicio ('asc' o 'desc').
- Descripción: Realiza búsqueda y filtrado con paginación.
- Respuesta exitosa (paginada):
```json
{
  "current_page": 1,
  "data": [ /* cursos filtrados */ ],
  "first_page_url": "...",
  "from": 1,
  "last_page": 5,
  "last_page_url": "...",
  "next_page_url": "...",
  "path": "...",
  "per_page": 20,
  "prev_page_url": null,
  "to": 20,
  "total": 100
}
```
---

## Notas para el Frontend

- Todas las respuestas están en formato JSON.
- Si ocurre un error, revisa el campo `status` y el mensaje correspondiente.

---

## Notificaciones Push (Firebase Cloud Messaging HTTP v1)

### Requisitos previos
- Archivo de credenciales JSON de Service Account de Firebase (descargar desde la consola de Firebase, sección Cuentas de servicio).
- Instalar la librería JWT para PHP:

```bash
composer require firebase/php-jwt
```

### Configuración
1. Coloca el archivo JSON en una ruta segura, por ejemplo:
   `storage/app/private/firebase/service-account.json`
2. Agrega la ruta absoluta en tu archivo `.env`:
   ```
   FIREBASE_CREDENTIALS=/ruta/absoluta/a/storage/app/private/firebase/service-account.json
   ```
3. Para que las notificaciones push se envíen inmediatamente (sin necesidad de un worker de colas), ajusta la siguiente variable en tu archivo `.env`:
   ```
   QUEUE_CONNECTION=sync
   ```
   **¿Por qué este cambio?**
   Por defecto, Laravel usa un sistema de colas para procesar tareas en segundo plano (como el envío de notificaciones push). Si no tienes un worker ejecutándose (por ejemplo, en Docker sin un servicio adicional para la cola), los jobs quedan pendientes y no se envían hasta que se procese la cola manualmente. Al usar `QUEUE_CONNECTION=sync`, los jobs se ejecutan inmediatamente en el mismo request, lo que simplifica el flujo en entornos donde no se desea gestionar un worker de colas.

### Endpoints principales

#### Guardar/actualizar token FCM del usuario autenticado
- **POST** `/api/fcm-token`
- Body:
  ```json
  {
    "fcm_token": "TOKEN_FCM",
    "device_id": "ID_UNICO_DEL_DISPOSITIVO"
  }
  ```

#### Enviar notificación push (solo admin)
- **POST** `/api/notifications/send-fcm-v1`
- Body:
  ```json
  {
    "token": "TOKEN_FCM_DEL_DISPOSITIVO",
    "title": "Título de la notificación",
    "body": "Mensaje de la notificación"
  }
  ```

### ¿Cómo funciona?
- El backend genera un JWT firmado con el Service Account y obtiene un access token de Google.
- Se envía la notificación a FCM HTTP v1 usando ese token.
- El método implementado es `sendFcmV1` en `NotificationController`.

### Notas
- Solo usuarios autenticados y con rol admin pueden enviar notificaciones.
- Puedes adaptar el método para enviar a múltiples tokens si lo necesitas.
- Si cambias la ubicación del JSON, actualiza la variable en `.env`.

---

## Instrucciones para el equipo de la app móvil (Push Notifications)

### 1. Instalación y configuración en la app móvil

- Instala el SDK de Firebase Cloud Messaging (según tu framework: Ionic/Angular, React Native, Flutter, etc.).
- Configura el proyecto móvil con los datos de Firebase (`google-services.json` para Android, `GoogleService-Info.plist` para iOS).
- Solicita permisos de notificación al usuario.
- Obtén el token FCM del dispositivo tras el login o cuando cambie.

### 2. Registro del token FCM en el backend

- Envía el token FCM al backend cada vez que el usuario inicie sesión o el token cambie:
  - Endpoint: `POST /api/fcm-token`
  - Headers: `Authorization: Bearer <token_sanctum>`
  - Body:
    ```json
    {
      "fcm_token": "TOKEN_FCM_GENERADO",
      "device_id": "ID_UNICO_DEL_DISPOSITIVO"
    }
    ```
  - Puedes enviar también: `device_name`, `device_os`, `app_version` (opcional).

### 3. Prueba de notificaciones push

- Un administrador puede enviar una notificación desde el backend:
  - Endpoint: `POST /api/notifications/send-fcm-v1`
  - Body:
    ```json
    {
      "token": "TOKEN_FCM_DEL_DISPOSITIVO",
      "title": "Título de prueba",
      "body": "Mensaje de prueba"
    }
    ```
- El dispositivo debe recibir la notificación push.

### 4. Consideraciones

- Si el token FCM cambia (por ejemplo, tras reinstalar la app), vuelve a registrar el nuevo token usando el mismo endpoint.
- El backend solo enviará notificaciones a los tokens registrados correctamente.

### 5. Recursos útiles
- [Documentación oficial FCM Android](https://firebase.google.com/docs/cloud-messaging/android/client)
- [Documentación oficial FCM iOS](https://firebase.google.com/docs/cloud-messaging/ios/client)
- [Documentación oficial FCM Web](https://firebase.google.com/docs/cloud-messaging/js/client)

---

# Endpoints para integración de la app móvil

## 1. Registro de dispositivo (sin usuario, sin autenticación)

- **POST** `/api/auth/device/register`
- **Body:**
```json
{
  "device_id": "uuid-del-dispositivo",
  "fcm_token": "token-fcm",
  "device_name": "Nombre del dispositivo",
  "device_os": "Android/iOS/Web",
  "app_version": "1.0.0",
  "extra_data": { "foo": "bar" }
}
```
- **Respuesta:**
```json
{
  "ok": true,
  "device_id": "uuid-del-dispositivo"
}
```

## 2. Registro de usuario (asociando dispositivo)

- **POST** `/api/auth/register`
- **Body:**
```json
{
  "name": "Nombre",
  "email": "usuario@dom.com",
  "password": "secreto",
  "password_confirmation": "secreto",
  "device_id": "uuid-del-dispositivo",
  "fcm_token": "token-fcm"
}
```
- **Respuesta:**
```json
{
  "ok": true,
  "device_id": "uuid-del-dispositivo"
}
```

## 3. Login de usuario (opcionalmente actualiza FCM)

- **POST** `/api/auth/login`
- **Body:**
```json
{
  "email": "usuario@dom.com",
  "password": "secreto",
  "device_id": "uuid-del-dispositivo",
  "fcm_token": "token-fcm"
}
```
- **Respuesta:**
```json
{
  "user": { ... },
  "token": "token-sanctum"
}
```

## 4. Actualizar info de dispositivo (requiere autenticación)

- **POST** `/api/auth/device`
- **Headers:**
  - `Authorization: Bearer <token-sanctum>`
- **Body:**
```json
{
  "device_id": "uuid-del-dispositivo",
  "fcm_token": "token-fcm",
  "device_name": "Nombre del dispositivo",
  "device_os": "Android/iOS/Web",
  "app_version": "1.0.0",
  "extra_data": { "foo": "bar" }
}
```
- **Respuesta:**
```json
{
  ...datos del dispositivo actualizado...
}
```

## 5. Guardar/actualizar token FCM (requiere autenticación)

- **POST** `/api/fcm-token`
- **Headers:**
  - `Authorization: Bearer <token-sanctum>`
- **Body:**
```json
{
  "fcm_token": "token-fcm",
  "device_id": "uuid-del-dispositivo"
}
```

---

**Notas:**
- El primer registro de dispositivo no requiere autenticación.
- El resto de endpoints requieren el token de usuario (Sanctum) en la cabecera.
- Si el FCM token cambia, vuelve a registrar el dispositivo o usa `/api/fcm-token`.
- Para recibir notificaciones, el dispositivo debe tener el FCM token actualizado en backend.

---

## Notificaciones WhatsApp (WhatsApp Cloud API)

La aplicación permite enviar notificaciones de WhatsApp a través de la API de nube de Meta.

### Configuración

1. Obtén tu **token de acceso** y **ID de número de teléfono** en [Meta for Developers](https://developers.facebook.com/).
2. Añade las credenciales en el archivo `.env`:
   ```env
   WHATSAPP_TOKEN=tu_token
   WHATSAPP_PHONE_ID=tu_phone_id
   ```
3. El archivo `config/services.php` debe tener:
   ```php
   'whatsapp' => [
       'token' => env('WHATSAPP_TOKEN'),
       'phone_id' => env('WHATSAPP_PHONE_ID'),
   ],
   ```

### Uso desde el panel de administración

- Accede al menú lateral: **WhatsApp Notificación**.
- El formulario permite enviar una plantilla de WhatsApp (por defecto `hello_world`) a uno o varios números.
- El envío se realiza usando la API oficial de Meta.

### Ejemplo de envío (cURL)

```bash
curl -i -X POST \
  https://graph.facebook.com/v22.0/<PHONE_ID>/messages \
  -H 'Authorization: Bearer <TOKEN>' \
  -H 'Content-Type: application/json' \
  -d '{ "messaging_product": "whatsapp", "to": "34684245005", "type": "template", "template": { "name": "hello_world", "language": { "code": "en_US" } } }'
```

### Notas
- El token de acceso puede expirar; si ocurre un error 401, genera uno nuevo en Meta for Developers.
- Puedes modificar los números destinatarios en el servicio `WhatsAppService`.
- El sistema registra en el log de Laravel la respuesta de la API para cada envío.

---

## 📧 Sistema de Notificaciones por Email

El sistema de notificaciones por email permite enviar correos personalizados a los usuarios de la plataforma con un diseño profesional que coincide con la interfaz de administración.

### Características

- ✅ **Diseño Consistente**: Emails que coinciden con el estilo de la interfaz de administración
- ✅ **Interfaz de Administración**: Panel web para crear y enviar notificaciones
- ✅ **API REST**: Endpoints para envío programático
- ✅ **Comando Artisan**: Envío masivo desde línea de comandos
- ✅ **Filtrado de Usuarios**: Por rol, estado activo, o usuarios específicos
- ✅ **Botones de Acción**: Enlaces personalizables en los emails
- ✅ **Colas de Trabajo**: Soporte para envío asíncrono
- ✅ **Plantillas Responsivas**: Diseño optimizado para móviles

### Configuración Inicial

#### 1. Configurar Variables de Entorno

Edita tu archivo `.env` con la configuración de email:

**Para Gmail (Recomendado para producción y entrega real):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**⚠️ Configuración de Gmail paso a paso:**
1. **Habilitar verificación en 2 pasos** en tu cuenta de Google
2. **Ir a Configuración de Google** → Seguridad → Verificación en 2 pasos
3. **Generar contraseña de aplicación**:
   - Ve a "Contraseñas de aplicaciones"
   - Selecciona "Correo" y "Otro (nombre personalizado)"
   - Escribe "Laravel GAP 2025"
   - Copia la contraseña generada (16 caracteres)
4. **Usar la contraseña de aplicación** en `MAIL_PASSWORD` (no tu contraseña normal)

**Para Mailtrap (Solo para desarrollo/testing):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=tu_usuario_mailtrap
MAIL_PASSWORD=tu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@dominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

**⚠️ Nota sobre Mailtrap:**
- Mailtrap **NO entrega emails reales** - solo los captura para testing
- Los emails aparecen en tu sandbox de Mailtrap pero no llegan a destinatarios reales
- Perfecto para desarrollo, pero no para producción

**Obtener credenciales de Mailtrap (si eliges usarlo para testing):**
1. Regístrate en [Mailtrap.io](https://mailtrap.io) (cuenta gratuita)
2. Crea un nuevo inbox
3. Ve a **SMTP Settings**
4. Copia las credenciales que aparecen

#### 2. Limpiar Caché de Configuración

```bash
# Dentro del contenedor Docker
docker exec -it GAP_laravel php artisan config:clear
docker exec -it GAP_laravel php artisan route:clear
docker exec -it GAP_laravel php artisan view:clear
```

#### 3. Verificar Configuración

Después de configurar las credenciales, verifica que todo esté correcto:

```bash
# Verificar que las credenciales se cargaron correctamente
docker exec -it GAP_laravel php artisan config:show mail

# Para Gmail deberías ver algo como:
# 'username' => 'tu-email@gmail.com'
# 'password' => 'abcd efgh ijkl mnop' (contraseña de aplicación de 16 caracteres)
# 'host' => 'smtp.gmail.com'
# Si ves 'null', las credenciales no se cargaron correctamente

# Prueba rápida de envío (los emails llegarán a tu bandeja de entrada real)
docker exec -it GAP_laravel php artisan email:send-notification \
  --subject="Prueba de Email desde GAP 2025" \
  --body="Si recibes este email, la configuración de Gmail está funcionando correctamente." \
  --active-only
```

### Métodos de Uso

#### 1. Interfaz de Administración (Recomendado)

Accede al panel de administración:
- **URL**: `/admin/email-notifications`
- **Crear**: `/admin/email-notifications/create`
- **Navegación**: Admin → Notificaciones → Email Notifications

**Características del panel:**
- Vista previa en tiempo real del email
- Filtrado de usuarios por rol y estado
- Envío de emails de prueba
- Selección masiva de destinatarios
- Estadísticas de envío

#### 2. Comando Artisan (Para envío masivo)

```bash
# Envío básico a usuarios activos
docker exec -it GAP_laravel php artisan email:send-notification \
  --subject="Bienvenido a GAP 2025" \
  --body="Bienvenido a nuestra plataforma educativa." \
  --active-only

# Envío completo con botón de acción
docker exec -it GAP_laravel php artisan email:send-notification \
  --subject="Nuevo Curso Disponible" \
  --greeting="Estimado estudiante," \
  --body="Se ha añadido un nuevo curso a tu plan de estudios. Haz clic en el botón para acceder." \
  --action-text="Ver Curso" \
  --action-url="https://tu-dominio.com/cursos/nuevo" \
  --footer="¡Feliz aprendizaje!" \
  --active-only

# Ver todas las opciones disponibles
docker exec -it GAP_laravel php artisan email:send-notification --help
```

#### 3. API REST (Para integración programática)

**Enviar a múltiples usuarios:**
```bash
curl -X POST http://tu-dominio.com/api/email-notifications/send \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TU_TOKEN_ADMIN" \
  -d '{
    "subject": "Actualización del Sistema",
    "greeting": "Hola,",
    "body": "El sistema ha sido actualizado con nuevas funcionalidades.",
    "action_text": "Explorar Funciones",
    "action_url": "https://tu-dominio.com/funciones",
    "active_only": true
  }'
```

**Enviar a usuario específico:**
```bash
curl -X POST http://tu-dominio.com/api/email-notifications/send-to-user/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer TU_TOKEN_ADMIN" \
  -d '{
    "subject": "Mensaje Personal",
    "body": "Este es un mensaje personalizado para ti."
  }'
```

#### 4. Uso Programático en Código

```php
use App\Models\User;
use App\Notifications\CustomEmailNotification;

// Enviar a un usuario específico
$user = User::find(1);
$notification = new CustomEmailNotification(
    subject: 'Bienvenido',
    greeting: '¡Hola!',
    body: 'Bienvenido a nuestra plataforma.',
    actionText: 'Comenzar',
    actionUrl: 'https://tu-dominio.com/dashboard'
);
$user->notify($notification);

// Envío masivo con colas
use Illuminate\Support\Facades\Notification;

$users = User::where('status', 1)->get();
Notification::send($users, $notification);
```

### Personalización

#### Modificar la Plantilla de Email

Edita el archivo `resources/views/emails/custom-notification.blade.php` para:
- Cambiar colores y estilos
- Añadir tu logo
- Modificar el diseño
- Actualizar el pie de página

#### Añadir Nuevos Parámetros

Modifica `app/Notifications/CustomEmailNotification.php` para añadir nuevos campos personalizables.

### Optimización para Producción

#### 1. Configurar Colas de Trabajo

```env
QUEUE_CONNECTION=database
```

```bash
# Ejecutar worker de colas
docker exec -it GAP_laravel php artisan queue:work
```

#### 2. Configurar Proveedor de Email

Para producción, considera usar:
- **Amazon SES**
- **SendGrid**
- **Mailgun**
- **Postmark**

### Solución de Problemas

#### Error 504 Gateway Timeout

```bash
# Reiniciar contenedores
docker-compose restart

# Verificar estado de contenedores
docker ps

# Limpiar cachés
docker exec -it GAP_laravel php artisan config:clear
docker exec -it GAP_laravel php artisan route:clear
```

#### Rutas no funcionan

```bash
# Verificar que las rutas de autenticación estén cargadas
docker exec -it GAP_laravel php artisan route:list | findstr login

# Si no aparecen, verificar bootstrap/app.php
```

#### Emails no se envían

**Problema más común: Credenciales no configuradas**

Si ves `MAIL_USERNAME=null` y `MAIL_PASSWORD=null` en tu `.env`, necesitas configurar las credenciales reales:

```bash
# 1. Verificar configuración actual
docker exec -it GAP_laravel php artisan config:show mail

# 2. Si las credenciales son null, actualizar .env con credenciales reales
# Para Gmail:
# MAIL_USERNAME=tu-email@gmail.com
# MAIL_PASSWORD=tu-contraseña-de-aplicacion-de-16-caracteres

# 3. Limpiar caché después de cambiar .env
docker exec -it GAP_laravel php artisan config:clear

# 4. Probar envío
docker exec -it GAP_laravel php artisan email:send-notification \
  --subject="Test Email" \
  --body="Prueba de configuración de email" \
  --active-only

# 5. Verificar logs para errores
docker exec -it GAP_laravel tail -f storage/logs/laravel.log

# 6. Prueba manual con tinker
docker exec -it GAP_laravel php artisan tinker
# En tinker: Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

**Problemas específicos de Gmail:**

- **Error de autenticación**: Asegúrate de usar una contraseña de aplicación, no tu contraseña normal
- **Verificación en 2 pasos**: Debe estar habilitada para generar contraseñas de aplicación
- **Cuenta bloqueada**: Google puede bloquear el acceso si detecta actividad sospechosa
- **Límites de envío**: Gmail tiene límites diarios de envío (500 emails/día para cuentas gratuitas)

**Otros problemas comunes:**

- **Puerto bloqueado**: Algunos ISP bloquean el puerto 587, prueba el puerto 465 con SSL
- **Configuración de firewall**: Verificar que Docker pueda acceder a SMTP
- **Emails van a spam**: Configura SPF, DKIM y DMARC en tu dominio

**Configuraciones alternativas para Gmail:**

```env
# Puerto 465 con SSL (alternativa)
MAIL_PORT=465
MAIL_ENCRYPTION=ssl

# Puerto 587 con TLS (recomendado)
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

### Archivos del Sistema

- **Notificación**: `app/Notifications/CustomEmailNotification.php`
- **Mailable**: `app/Mail/CustomNotificationMail.php`
- **Plantilla**: `resources/views/emails/custom-notification.blade.php`
- **Comando**: `app/Console/Commands/SendEmailNotification.php`
- **Controlador Admin**: `app/Http/Controllers/Admin/EmailNotificationController.php`
- **Controlador API**: `app/Http/Controllers/Api/EmailNotificationController.php`
- **Vistas Admin**: `resources/views/admin/email-notifications/`

---

## 📄 Acceso y descarga de archivos en Laravel (storage) con Docker

Para que la descarga de archivos (como temarios PDF) funcione correctamente en todos los entornos Docker:

1. **El enlace simbólico `public/storage` debe crearse dentro del contenedor app** (no solo en tu máquina):
   
   Reemplaza `<nombre-contenedor-app>` por el nombre de tu contenedor (por ejemplo, `LaravelGAP2025-app`):
   ```bash
   docker exec -it <nombre-contenedor-app> php artisan storage:link
   ```
   Si ya existe y apunta mal, elimínalo y vuelve a crearlo:
   ```bash
   docker exec -it <nombre-contenedor-app> rm /var/www/public/storage
   docker exec -it <nombre-contenedor-app> php artisan storage:link
   ```

2. **Verifica que los archivos subidos estén en `/var/www/storage/app/public/temarios` dentro del contenedor.**

3. **Asegúrate de que los permisos de las carpetas y archivos permitan la lectura:**
   ```bash
   docker exec -it <nombre-contenedor-app> chmod -R 755 /var/www/storage/app/public
   ```

4. **La ruta de descarga en las vistas debe ser:**
   ```blade
   href="{{ asset('storage/' . $curso->temario_path) }}"
   ```

5. **Si usas volúmenes en Docker, asegúrate de que `./www` esté correctamente montado en `/var/www` en todos los servicios necesarios (app, nginx).**

> Si tienes problemas de acceso (error 403 o 404), revisa los pasos anteriores y consulta con el equipo.

---

## 💬 Implementación del Chat entre Usuarios (Arquitectura Hexagonal)

El sistema de chat permite la comunicación entre usuarios (alumnos y profesores) y está implementado siguiendo una arquitectura hexagonal (Ports & Adapters) para facilitar el mantenimiento y la escalabilidad.

### Estructura principal
- **Dominio:**
  - `app/Domain/Chat/Message.php`: Entidad de mensaje.
  - `app/Domain/Chat/ChatRepositoryInterface.php`: Interfaz del repositorio de chat.
- **Aplicación (Casos de uso):**
  - `app/Application/Chat/SendMessage.php`: Enviar mensajes.
  - `app/Application/Chat/GetMessagesBetweenUsers.php`: Obtener mensajes entre dos usuarios.
  - `app/Application/Chat/GetLastChatsForUser.php`: Obtener últimos chats recientes.
  - `app/Application/Chat/GetUnreadCountForUser.php`: Contar mensajes no leídos.
  - `app/Application/Chat/MarkMessagesAsRead.php`: Marcar mensajes como leídos.
- **Infraestructura:**
  - `app/Infrastructure/Chat/EloquentChatRepository.php`: Implementación con Eloquent.
  - `app/Models/ChatMessage.php`: Modelo Eloquent para la tabla `chat_messages`.
- **Controladores:**
  - `app/Http/Controllers/ChatController.php`: Orquesta los casos de uso y la vista web.
  - `app/Http/Controllers/Api/ChatApiController.php`: API REST para aplicaciones móviles.

### API REST para Aplicaciones Móviles

El sistema incluye una API completa para integración con aplicaciones móviles (Ionic Angular, React Native, etc.):

#### Endpoints Principales:
- **GET** `/api/chat/overview` - Vista general del chat (chats recientes + usuarios disponibles)
- **GET** `/api/chat/users` - Lista de usuarios disponibles para chatear
- **GET** `/api/chat/users/search` - Búsqueda de usuarios por rol y nombre
- **GET** `/api/chat/recent` - Conversaciones recientes
- **GET** `/api/chat/conversation/{userId}` - Mensajes de una conversación específica
- **POST** `/api/chat/send/{userId}` - Enviar mensaje a un usuario

#### Características de la API:
- Autenticación con Laravel Sanctum
- Respuestas JSON estructuradas
- Manejo de errores completo
- Optimizada para aplicaciones móviles
- Documentación completa en `CHAT_API_DOCUMENTATION.md`

#### Ejemplo de uso en Ionic Angular:
```typescript
// Cargar pantalla principal del chat
getChatOverview(): Observable<any> {
  return this.http.get(`${this.apiUrl}/chat/overview`, this.getHeaders());
}

// Enviar mensaje
sendMessage(userId: number, message: string): Observable<any> {
  return this.http.post(`${this.apiUrl}/chat/send/${userId}`,
    { message }, this.getHeaders());
}
```

### Migraciones
- `database/migrations/2025_06_30_000000_create_chat_messages_table.php`: Crea la tabla principal del chat.

### Vistas Web
- `resources/views/chat/index.blade.php`: Lista de usuarios y chats recientes.
- `resources/views/chat/show.blade.php`: Conversación entre dos usuarios.
- En el home del alumno (`resources/views/alumno/home.blade.php`), la tarjeta de chat muestra los chats recientes.

### Características
- Comunicación en tiempo real entre usuarios
- Interfaz web y API móvil
- Búsqueda de usuarios por rol
- Historial de conversaciones
- Arquitectura desacoplada y escalable

### Notas
- El modelo y la tabla antigua `messages` han sido eliminados para evitar confusiones.
- La API está optimizada para aplicaciones móviles con endpoints eficientes.
- Consulta `CHAT_API_DOCUMENTATION.md` para documentación completa de la API.

---
