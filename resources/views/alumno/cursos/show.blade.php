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
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="text-primary mb-3">Descripción del Curso</h5>
                        <p class="mb-4">{{ $curso->descripcion ?? 'Sin descripción disponible.' }}</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-2">Información General</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="ri-calendar-line text-primary me-2"></i>
                                        <strong>Fechas:</strong> {{ $curso->fechaInicio->format('d/m/Y') }} - {{ $curso->fechaFin->format('d/m/Y') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="ri-group-line text-primary me-2"></i>
                                        <strong>Plazas:</strong> {{ $curso->plazas }} ({{ $curso->getPlazasDisponibles() }} disponibles)
                                    </li>
                                    <li class="mb-2">
                                        <i class="ri-time-line text-primary me-2"></i>
                                        <strong>Estado:</strong> 
                                        <span class="badge bg-{{ $curso->estado == 'activo' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($curso->estado) }}
                                        </span>
                                    </li>
                                    @if($curso->precio)
                                    <li class="mb-2">
                                        <i class="ri-money-euro-circle-line text-primary me-2"></i>
                                        <strong>Precio:</strong> €{{ number_format($curso->precio, 2) }}
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-2">Detalles Adicionales</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="ri-calendar-check-line text-primary me-2"></i>
                                        <strong>Duración:</strong> {{ $curso->fechaInicio->diffInDays($curso->fechaFin) }} días
                                    </li>
                                    <li class="mb-2">
                                        <i class="ri-user-star-line text-primary me-2"></i>
                                        <strong>Inscritos:</strong> {{ $curso->getInscritosCount() }} alumnos
                                    </li>
                                    <li class="mb-2">
                                        <i class="ri-percent-line text-primary me-2"></i>
                                        <strong>Ocupación:</strong> {{ $curso->getPorcentajeOcupacion() }}%
                                    </li>
                                    @if($curso->temario_path)
                                    <li class="mb-2">
                                        <i class="ri-file-text-line text-primary me-2"></i>
                                        <strong>Temario:</strong> 
                                        <a href="{{ asset('storage/' . $curso->temario_path) }}" target="_blank" class="text-decoration-none">
                                            Descargar temario
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if($curso->portada_path)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $curso->portada_path) }}" 
                                 class="img-fluid rounded" alt="{{ $curso->titulo }}"
                                 style="max-height: 300px; object-fit: cover;">
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Sección de diploma --}}
                <div class="mt-4">
                    <h5><i class="ri-award-line me-2"></i> Diploma del curso</h5>
                    @if($diploma)
                        <a href="{{ $diploma->url_pdf }}" target="_blank" class="btn btn-success">
                            <i class="ri-download-line me-1"></i> Descargar mi diploma
                        </a>
                    @else
                        <button id="solicitar-diploma-btn" class="btn btn-primary">
                            <i class="ri-file-text-line me-1"></i> Solicitar diploma
                        </button>
                    @endif
                </div>
                @if($estaInscrito)
                    <div class="alert alert-info mt-3">
                        <i class="ri-information-line me-2"></i>
                        Ya estás inscrito en este curso.
                        <strong>Estado: </strong>
                        <span class="badge bg-{{ $estadoInscripcion === 'activo' ? 'success' : ($estadoInscripcion === 'pendiente' ? 'warning' : 'secondary') }}">
                            @if($estadoInscripcion === 'activo') Aceptado
                            @elseif($estadoInscripcion === 'pendiente') Pendiente
                            @elseif($estadoInscripcion === 'espera') En espera
                            @elseif($estadoInscripcion === 'rechazado') Rechazado
                            @else Desconocido
                            @endif
                        </span>
                    </div>
                @else
                    @if($curso->estado === 'activo' && $curso->tienePlazasDisponibles())
                        <form method="POST" action="{{ route('alumno.cursos.solicitar', $curso->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success mt-3">
                                <i class="ri-user-add-line me-1"></i> Solicitar inscripción en este curso
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="ri-error-warning-line me-2"></i>
                            @if($curso->estado !== 'activo')
                                Este curso no está disponible para inscripción.
                            @elseif(!$curso->tienePlazasDisponibles())
                                Este curso no tiene plazas disponibles.
                            @endif
                        </div>
                    @endif
                @endif
                <a href="{{ route('alumno.home') }}" class="btn btn-secondary mt-3 ms-2">
                    <i class="ri-arrow-left-line me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('solicitar-diploma-btn');
    if (btn) {
        btn.addEventListener('click', function() {
            if (!confirm('¿Estás seguro de que quieres solicitar tu diploma?')) return;
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Generando...';
            fetch(`/admin/cursos/{{ $curso->id }}/diploma/participante/{{ auth()->user()->persona->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('¡Diploma generado correctamente!');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-file-text-line me-1"></i> Solicitar diploma';
                }
            })
            .catch(() => {
                alert('Error al generar el diploma. Inténtalo de nuevo.');
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-file-text-line me-1"></i> Solicitar diploma';
            });
        });
    }
});
</script>
@endpush
