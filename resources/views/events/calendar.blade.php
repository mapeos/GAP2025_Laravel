@extends('template.base-alumno')

@section('title', 'Calendario de Eventos')
@section('title-page', 'Calendario')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-3">
        <li class="breadcrumb-item">
            <a href="{{ route('alumno.home') }}"><i class="ri-home-2-line"></i> Inicio</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Calendario de Eventos</li>
    </ol>
</nav>
@endsection

@section('content')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- Calendar CSS -->
@include('events.partials.calendar-styles')

<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    
    <!-- Botones de acción -->
    @include('events.partials.action-buttons')

    <!-- Modales -->
    @include('events.partials.solicitud-cita-modal')
    @include('events.partials.solicitud-cita-modal-ai')
    @include('events.partials.crear-evento-modal')
    @include('events.partials.editar-evento-modal')

    <!-- Vista del calendario -->
    @include('events.partials.calendar-view')

    <!-- Botón flotante para crear evento -->
    @include('events.partials.floating-button')
</div>

<!-- Calendar JavaScript -->
@include('events.partials.calendar-scripts')

@endsection

