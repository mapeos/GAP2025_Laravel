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
>>>>>>> 5179a36 (CRUD de noticias y categorias (pendiente de terminar las vistas))
