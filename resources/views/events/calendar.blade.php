@extends('template.base')

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

