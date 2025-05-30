# GAP2025_Laravel
> Codigo compartido de la aplicación de gestión de formación para el curso impartido de "Desarrollo de aplicaciones con tecnologías web"

Se parte de una instalación limpia de Laravel 12, starter kit, con Livewire y Volt

# Documento compartido para colaboración
https://docs.google.com/document/d/1gK_PZprqpSuYv6uTT6py5OTcnto8C4YHWykpBa0ruH4/edit?pli=1&tab=t.0

# Funcionalidades
## Parte de backoffice
- Gestion de usuarios
- Gestion de comunicaciones
- - Push
- - Whatsapp
- - Mail

# Plantilla
- Plantilla base: @extends('template.base')
- Secciones:
- - title: Atributo "title" de la página
- - title-sidebar: Texto en la parte superior del menu lateral
- - title-page: Titulo de la sección en la parte de contenido de la página
- - content: Todo el contenido de la página
- - breadcrumb: Recorrido de la página actual

- Inyección de recursos (stack);
- - css: en la cabecera de la página. @push('css')
- - js: en el pie de la página. @push('js')

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

---

## 5. Próximos Pasos
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


# Gestión de usuarios y roles (Miguel)

## 1. Introducción
El sistema de gestión de usuarios permite administrar el acceso a las diferentes secciones de la aplicación web, cada usuario visualizará únicamente las funcionalidades correspondientes a su rol: **Administrador**, **Profesor** o **Alumno**. Esta gestión se implementa utilizando el sistema de autenticación y autorización de Laravel.

---

## 2. Estructura de Vistas
Se utilizará una estructura organizada dentro del directorio `resources/views/private/`, donde se almacenarán todas las vistas privadas accesibles únicamente tras autenticación.

### Directorio propuesto:
```
resources/views/private/
├── admin/ ← Vistas para Administrador
├── profesores/ ← Vistas para Profesores
└── alumnos/ ← Vistas para Alumnos
```

---

## 3. Rutas Protegidas
Las rutas privadas se definirán en `routes/web.php` y estarán protegidas mediante el middleware `auth`. Además, se establecerá una lógica de redirección tras el inicio de sesión según el rol del usuario autenticado.

### Ejemplo de definición de rutas protegidas:
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/profesores', [ProfesorController::class, 'index'])->name('profesores.dashboard');
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.dashboard');
});
```

---

## 4. Redirección por Rol al Iniciar Sesión
En el controlador de autenticación (`LoginController` o el método `authenticated` del `Auth\LoginController`), se redirigirá al usuario a su vista correspondiente según su rol.

### Ejemplo en `LoginController`:
```php
protected function authenticated(Request $request, $user)
{
    switch ($user->rol) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'profesor':
            return redirect()->route('profesores.dashboard');
        case 'alumno':
            return redirect()->route('alumnos.dashboard');
        default:
            auth()->logout();
            return redirect('/login')->withErrors(['rol' => 'Rol no autorizado.']);
    }
}
```

---

## 5. Asignación de Roles
Los roles deben almacenarse en la base de datos, ya sea como campo `rol` en la tabla `users`, o mediante una relación con una tabla de roles. En esta primera fase he optado por algo sencillo, almacenando el rol como un campo string directamente en la tabla `users`, pero se podrá modificar cuando empiece a trabajar en los modelos y migraciones.

### Migración ejemplo:
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('rol')->default('alumno'); // Valores posibles: 'admin', 'profesor', 'alumno'
});
```

---

## 6. Vistas Diferenciadas
Cada rol tendrá su propio panel con funcionalidades específicas:

- **Administrador**: Gestión de usuarios, asignación de roles, estadísticas generales.
- **Profesor**: Gestión de clases, alumnos inscritos.
- **Alumno**: Acceso a clases disponibles, progreso, asistencia.

---
