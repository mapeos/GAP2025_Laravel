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


