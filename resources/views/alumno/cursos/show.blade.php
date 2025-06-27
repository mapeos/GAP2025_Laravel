@extends('template.base-alumno')

@section('title', 'Detalle del Curso')
@section('title-page', $curso->titulo)

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-3">
        <li class="breadcrumb-item"><a href="{{ route('alumno.home') }}"><i class="ri-home-2-line"></i> Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $curso->titulo }}</li>
    </ol>
</nav>
@endsection

@section('content')
    <div class="container my-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="ri-book-open-line me-2"></i> {{ $curso->titulo }}</h4>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Descripción:</strong> {{ $curso->descripcion }}</p>
                <p class="mb-2"><strong>Fechas:</strong> {{ $curso->fechaInicio }} - {{ $curso->fechaFin }}</p>
                <p class="mb-2"><strong>Plazas:</strong> {{ $curso->plazas }}</p>
                <p class="mb-2"><strong>Estado:</strong> <span class="badge bg-{{ $curso->estado == 'activo' ? 'success' : 'secondary' }}">{{ ucfirst($curso->estado) }}</span></p>
                <a href="{{ route('alumno.cursos.inscribir', $curso->id) }}" class="btn btn-success mt-3">
                    <i class="ri-user-add-line me-1"></i> Solicitar inscripción en este curso
                </a>
                <a href="{{ route('alumno.home') }}" class="btn btn-secondary mt-3 ms-2">
                    <i class="ri-arrow-left-line me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
@endsection
