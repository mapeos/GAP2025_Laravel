@extends('template.base-facultativo')

@section('title', 'Calendario Médico')
@section('title-page', 'Calendario')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('facultativo.home') }}"><i class="ri-home-2-line"></i> Inicio</a>
</li>
<li class="breadcrumb-item active" aria-current="page">Calendario Médico</li>
@endsection

@section('content')

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- Calendar CSS -->
@include('facultativo.partials.calendar-styles')

<div class="container">
    <h1 class="mb-4">Calendario Médico</h1>
    
    <!-- Botones de acción -->
    @include('facultativo.partials.action-buttons')

    <!-- Vista del calendario -->
    @include('facultativo.partials.calendar-view')

    <!-- Botón flotante para crear evento -->
    @include('facultativo.partials.floating-button')

    <!-- Modales -->
    @include('facultativo.partials.solicitud-cita-modal-ai')
    @include('facultativo.partials.crear-evento-modal')
    @include('facultativo.partials.editar-evento-modal')
</div>

<!-- Calendar JavaScript -->
@include('facultativo.partials.calendar-scripts')
</div>

@endsection 