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

Se utiliza un sistema basado en LLM basado en _Ollama + Mistral 7B_

-   Ollama es una plataforma de LLMs que se ejecuta en local
-   Mistral 7B es un modelo especializado en instrucciones, entiende cosas como "sugiereme un hueco en la agenda"

### Guía de Implementación Local de Ollama

Para implementar Ollama en tu versión local del proyecto, sigue estos pasos según tu sistema operativo:

#### Opciones de Implementación

##### A. Implementación con GPU NVIDIA (Máximo Rendimiento)

Si tienes una GPU NVIDIA, puedes usar esta configuración optimizada:

1. **Crear el Dockerfile.ai.gpu**
   Crea un archivo `Dockerfile.ai.gpu` en la raíz del proyecto con este contenido:

    ```dockerfile
    FROM nvidia/cuda:12.1.0-runtime-ubuntu22.04

    # Install system dependencies
    RUN apt-get update && apt-get install -y \
        python3 \
        python3-pip \
        curl \
        && rm -rf /var/lib/apt/lists/*

    # Install Ollama
    RUN curl -fsSL https://ollama.com/install.sh | sh

    # Set working directory
    WORKDIR /app

    # Expose Ollama port
    EXPOSE 11434

    # Start Ollama with GPU support
    CMD ["ollama", "serve"]
    ```

2. **Modificar docker-compose.yml**
   Usa esta configuración para el servicio AI:
    ```yaml
    ai:
        build:
            context: ./
            dockerfile: Dockerfile.ai.gpu
        container_name: gap2025_laravel-ai
        restart: unless-stopped
        ports:
            - "11434:11434"
        volumes:
            - ollama_data:/root/.ollama
        networks:
            - alumnos-gap
        deploy:
            resources:
                reservations:
                    devices:
                        - driver: nvidia
                          count: 1
                          capabilities: [gpu]
    ```

##### B. Implementación Estándar (CPU o GPU Básica)

Si no tienes GPU NVIDIA o prefieres una configuración más simple:

1. **Crear el Dockerfile.ai**
   Crea un archivo `Dockerfile.ai` en la raíz del proyecto con este contenido:

    ```dockerfile
    FROM ubuntu:22.04

    # Install system dependencies
    RUN apt-get update && apt-get install -y \
        python3 \
        python3-pip \
        curl \
        && rm -rf /var/lib/apt/lists/*

    # Install Ollama
    RUN curl -fsSL https://ollama.com/install.sh | sh

    # Set working directory
    WORKDIR /app

    # Expose Ollama port
    EXPOSE 11434

    # Start Ollama
    CMD ["ollama", "serve"]
    ```

2. **Modificar docker-compose.yml**
   Usa esta configuración para el servicio AI:
    ```yaml
    ai:
        build:
            context: ./
            dockerfile: Dockerfile.ai
        container_name: gap2025_laravel-ai
        restart: unless-stopped
        ports:
            - "11434:11434"
        volumes:
            - ollama_data:/root/.ollama
        networks:
            - alumnos-gap
    ```

##### C. Implementación en Linux (Sin GPU NVIDIA)

1. **Configurar el servicio**

    - Usa el mismo `Dockerfile.ai` y configuración de `docker-compose.yml` que en la implementación estándar.
    - No necesitas configuración adicional para GPU.

2. **Consideraciones de rendimiento**

    - El modelo funcionará más lento que con GPU NVIDIA
    - Se recomienda tener al menos 8GB de RAM disponible
    - Puedes ajustar el uso de memoria en el contenedor añadiendo estas líneas al servicio `ai` en `docker-compose.yml`:
        ```yaml
        deploy:
            resources:
                limits:
                    memory: 4G
        ```

3. **Iniciar el servicio**

    ```bash
    docker-compose up -d ai
    ```

4. **Cargar el modelo Mistral**

    ```bash
    docker exec -it gap2025_laravel-ai ollama pull mistral
    ```

5. **Verificar la instalación**
    ```bash
    docker exec -it gap2025_laravel-ai ollama list
    ```

**Nota**: La versión con GPU NVIDIA ofrecerá un rendimiento significativamente mejor para el procesamiento de inferencias, pero requiere:

-   GPU NVIDIA compatible
-   Drivers NVIDIA actualizados
-   NVIDIA Container Toolkit instalado

3. **Configurar variables de entorno**
   Añade estas variables a tu archivo `.env`:

    ```
    OLLAMA_HOST=ai
    OLLAMA_PORT=11434
    ```

4. **Iniciar el servicio**

    ```bash
    docker-compose up -d ai
    ```

5. **Cargar el modelo Mistral**

    ```bash
    docker exec -it gap2025_laravel-ai ollama pull mistral
    ```

6. **Verificar la instalación**

    ```bash
    docker exec -it gap2025_laravel-ai ollama list
    ```

7. **Interactuar con la IA**
   Puedes interactuar directamente con Mistral usando el comando:

    ```bash
    docker exec -it gap2025_laravel-ai ollama run mistral
    ```

    Esto abrirá una consola interactiva donde puedes hacer preguntas directamente a la IA.

    Si necesitas reiniciar el servicio de IA por algún motivo, puedes usar:

    ```bash
    docker-compose restart ai
    ```

    Si has modificado el Dockerfile.ai y necesitas reconstruir solo el servicio de IA:

    ```bash
    docker-compose up -d --build ai
    ```

Más adelante se podría incluir:

-   Phi-2 (Microsoft): modelo pequeño y muy preciso.
-   Gemma 2B Instruct (Google): excelente para correr en CPU, más pequeño que Mistral.
-   TinyLlama 1.1B: modelo de 1B de parámetros, ultra ligero.

Para probar el sistema se puede utilizar el comando:

```
php artisan appointments:suggest-ai 1 2 5 "2025-07-01"
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
