@extends('template.base')

@section('title', 'Cursos')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div class="flex-grow-1 text-center">
            <span class="fw-bold fs-3">Cursos</span>
        </div>
        <a href="{{ route('admin.cursos.create') }}"
           class="btn btn-warning btn-sm fw-bold shadow ms-3"
           title="Crear curso">
            <i class="ri-user-add-line me-1"></i> Crear curso
        </a>
    </div>
    <div class="card-body">
        {{-- Este contenedor es importante para inyectar dinámicamente los mensajes flash --}}
        <div id="flash-messages">
            @include('template.partials.alerts')
        </div>

        {{-- Spinner de carga --}}
        <div class="loading-spinner" id="loadingSpinner" style="display: none;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 mb-0" id="spinnerText" style="font-size: 1.2rem;">Cargando...</p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Plazas</th>
                    <th>Estado</th>
                    <th>Eliminado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cursos as $curso)
                    <tr @if ($curso->trashed()) class="table-danger" @endif>
                        <td>{{ $curso->id }}</td>
                        <td>
                            <a href="{{ route('admin.cursos.show', $curso->id) }}" class="fw-bold text-decoration-none {{ $curso->trashed() ? 'text-danger' : '' }}">
                                {{ $curso->titulo }}
                            </a>
                            @if ($curso->trashed())
                                <i class="ri-alert-line text-danger" title="Curso eliminado"></i>
                            @endif
                        </td>
                        <td>{{ $curso->fechaInicio }}</td>
                        <td>{{ $curso->fechaFin }}</td>
                        <td>
                            <div class="{{ $curso->getPlazasColorClass() }}">
                                <strong>{{ $curso->getPlazasDisponibles() }}</strong> / {{ $curso->plazas }}
                            </div>
                            <small class="text-muted">
                                {{ number_format($curso->getPorcentajeOcupacion(), 1) }}% ocupado
                            </small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch me-2">
                                    <input class="form-check-input toggle-estado" type="checkbox" 
                                           data-curso-id="{{ $curso->id }}" 
                                           {{ $curso->estado === 'activo' ? 'checked' : '' }}
                                           style="cursor: pointer; width: 2.5rem; height: 1.25rem;">
                                </div>
                                <span class="estado-texto fw-medium">
                                    {{ $curso->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @if ($curso->trashed())
                                {{ $curso->deleted_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.cursos.edit', $curso->id) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="Editar curso">
                                    <i class="ri-edit-line"></i>
                                </a>
                                @if($curso->trashed())
                                    <button class="btn btn-sm btn-success toggle-delete" 
                                            data-curso-id="{{ $curso->id }}" 
                                            title="Activar curso">
                                        <i class="ri-upload-2-line"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-danger toggle-delete" 
                                            data-curso-id="{{ $curso->id }}" 
                                            title="Eliminar curso">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No hay cursos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection