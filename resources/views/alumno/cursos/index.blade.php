@extends('template.base-alumno')

@section('title', 'Cursos Disponibles')
@section('title-page', 'Cursos Disponibles')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-3">
        <li class="breadcrumb-item"><a href="{{ route('alumno.home') }}">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cursos Disponibles</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Mis Cursos -->
    @if($misCursos->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="ri-book-open-line me-2"></i>
                        Mis Cursos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($misCursos as $curso)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-{{ $curso->pivot->estado === 'activo' ? 'success' : ($curso->pivot->estado === 'pendiente' ? 'warning' : 'secondary') }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $curso->titulo }}</h6>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($curso->descripcion, 100) }}
                                    </p>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="ri-calendar-line me-1"></i>
                                            {{ $curso->fechaInicio->format('d/m/Y') }} - {{ $curso->fechaFin->format('d/m/Y') }}
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-{{ $curso->pivot->estado === 'activo' ? 'success' : ($curso->pivot->estado === 'pendiente' ? 'warning' : 'secondary') }}">
                                            @if($curso->pivot->estado === 'activo') Aceptado
                                            @elseif($curso->pivot->estado === 'pendiente') Pendiente
                                            @elseif($curso->pivot->estado === 'espera') En espera
                                            @elseif($curso->pivot->estado === 'rechazado') Rechazado
                                            @else Desconocido
                                            @endif
                                        </span>
                                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-sm btn-outline-primary">
                                            Ver detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Cursos Disponibles -->
    <div class="row">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="ri-add-circle-line me-2"></i>
                        Cursos Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    @if($cursosDisponibles->count() > 0)
                        <div class="row">
                            @foreach($cursosDisponibles as $curso)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-success">
                                    @if($curso->portada_path)
                                    <img src="{{ asset('storage/' . $curso->portada_path) }}" 
                                         class="card-img-top" alt="{{ $curso->titulo }}"
                                         style="height: 200px; object-fit: cover;">
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $curso->titulo }}</h6>
                                        <p class="card-text text-muted small">
                                            {{ Str::limit($curso->descripcion, 100) }}
                                        </p>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="ri-calendar-line me-1"></i>
                                                {{ $curso->fechaInicio->format('d/m/Y') }} - {{ $curso->fechaFin->format('d/m/Y') }}
                                            </small>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="ri-group-line me-1"></i>
                                                {{ $curso->getPlazasDisponibles() }} plazas disponibles
                                            </small>
                                        </div>
                                        @if($curso->precio)
                                        <div class="mb-2">
                                            <small class="text-success fw-bold">
                                                <i class="ri-money-euro-circle-line me-1"></i>
                                                €{{ number_format($curso->precio, 2) }}
                                            </small>
                                        </div>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success">
                                                Disponible
                                            </span>
                                            <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="btn btn-sm btn-primary">
                                                Ver detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-book-open-line text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No hay cursos disponibles</h5>
                            <p class="text-muted">En este momento no hay cursos disponibles para inscripción.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 