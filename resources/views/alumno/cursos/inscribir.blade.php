@extends('template.base-alumno')

@section('title', 'Solicitar inscripción')
@section('title-page', 'Solicitar inscripción en ' . $curso->titulo)

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-3">
        <li class="breadcrumb-item"><a href="{{ route('alumno.home') }}"><i class="ri-home-2-line"></i> Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('alumno.cursos.show', $curso->id) }}">{{ $curso->titulo }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Solicitar inscripción</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container my-4">
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="ri-user-add-line me-2"></i> Solicitar inscripción</h4>
        </div>
        <div class="card-body">
            <h5 class="mb-3">{{ $curso->titulo }}</h5>
            <p><strong>Descripción:</strong> {{ $curso->descripcion }}</p>
            <p><strong>Fechas:</strong> {{ $curso->fechaInicio }} - {{ $curso->fechaFin }}</p>
            <p><strong>Precio:</strong> <span class="badge bg-info">{{ $curso->precio ? number_format($curso->precio, 2) . ' €' : 'Gratuito' }}</span></p>
            <form method="POST" action="{{ url('/alumno/cursos/' . $curso->id . '/inscribir') }}">
                @csrf
                <input type="hidden" name="curso_id" value="{{ $curso->id }}">
                <button type="submit" class="btn btn-success">
                    <i class="ri-user-add-line me-1"></i> Solicitar inscripción
                </button>
                <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-secondary ms-2">
                    <i class="ri-arrow-left-line me-1"></i> Volver
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
