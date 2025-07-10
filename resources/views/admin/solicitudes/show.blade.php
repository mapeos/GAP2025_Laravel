@extends('template.base')

@section('title', 'Detalles de Solicitud de Inscripción')
@section('title-page', 'Detalles de Solicitud')

@section('content')
<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="ri-user-add-line me-2"></i> 
                        Detalles de Solicitud de Inscripción
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información del Usuario -->
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="ri-user-line me-2"></i> 
                                        Información del Usuario
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Nombre completo:</strong><br>
                                        {{ $persona->getNombreCompletoAttribute() }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Email:</strong><br>
                                        {{ $persona->user->email ?? 'No disponible' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>DNI:</strong><br>
                                        {{ $persona->dni ?? 'No especificado' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Teléfono:</strong><br>
                                        {{ $persona->tfno ?? 'No especificado' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Fecha de registro:</strong><br>
                                        @if($persona->created_at)
                                            {{ $persona->created_at->format('d/m/Y H:i') }}
                                        @else
                                            No disponible
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Curso -->
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="ri-book-open-line me-2"></i> 
                                        Información del Curso
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Título:</strong><br>
                                        {{ $curso->titulo }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Descripción:</strong><br>
                                        {{ $curso->descripcion ?? 'Sin descripción' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Fechas:</strong><br>
                                        {{ $curso->fechaInicio->format('d/m/Y') }} - {{ $curso->fechaFin->format('d/m/Y') }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Plazas:</strong><br>
                                        {{ $curso->plazas }} ({{ $curso->getPlazasDisponibles() }} disponibles)
                                    </div>
                                    <div class="mb-3">
                                        <strong>Estado del curso:</strong><br>
                                        <span class="badge bg-{{ $curso->estado === 'activo' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($curso->estado) }}
                                        </span>
                                    </div>
                                    @if($curso->precio)
                                    <div class="mb-3">
                                        <strong>Precio:</strong><br>
                                        €{{ number_format($curso->precio, 2) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado de la Solicitud -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="ri-time-line me-2"></i> 
                                        Estado de la Solicitud
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <strong>Estado actual:</strong><br>
                                                <span class="badge 
                                                    @if($participacion->estado == 'pendiente') bg-warning text-dark
                                                    @elseif($participacion->estado == 'activo') bg-success
                                                    @elseif($participacion->estado == 'rechazado') bg-danger
                                                    @else bg-secondary
                                                    @endif
                                                ">
                                                    @if($participacion->estado == 'pendiente') Pendiente
                                                    @elseif($participacion->estado == 'activo') Aceptado
                                                    @elseif($participacion->estado == 'rechazado') Rechazado
                                                    @else Desconocido
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Fecha de solicitud:</strong><br>
                                                @if($participacion->created_at)
                                                    {{ $participacion->created_at->format('d/m/Y H:i') }}
                                                @else
                                                    No disponible
                                                @endif
                                            </div>
                                            <div class="mb-3">
                                                <strong>Última actualización:</strong><br>
                                                @if($participacion->updated_at)
                                                    {{ $participacion->updated_at->format('d/m/Y H:i') }}
                                                @else
                                                    No disponible
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <form method="POST" action="{{ route('admin.solicitudes.update', [$curso->id, $persona->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-3">
                                                    <label for="estado" class="form-label">
                                                        <strong>Cambiar estado:</strong>
                                                    </label>
                                                    <select name="estado" id="estado" class="form-select">
                                                        <option value="pendiente" {{ $participacion->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                        <option value="activo" {{ $participacion->estado == 'activo' ? 'selected' : '' }}>Aceptar</option>
                                                        <option value="rechazado" {{ $participacion->estado == 'rechazado' ? 'selected' : '' }}>Rechazar</option>
                                                    </select>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="ri-save-line me-1"></i>
                                                        Actualizar Estado
                                                    </button>
                                                    <a href="{{ route('admin.solicitudes.index') }}" class="btn btn-secondary">
                                                        <i class="ri-arrow-left-line me-1"></i>
                                                        Volver a la Lista
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 