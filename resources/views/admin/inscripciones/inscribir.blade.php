@extends('template.base')

@section('title', 'Inscribir Personas - ' . $curso->titulo)
@section('title-sidebar', 'Gestión de Cursos')
@section('title-page', 'Inscribir Personas en Curso')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.cursos.index') }}">Cursos</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.cursos.show', $curso->id) }}">{{ $curso->titulo }}</a>
    </li>
    <li class="breadcrumb-item active">Inscribir Personas</li>
@endsection

@section('content')
<div class="row">
    <!-- Información del curso -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-book-open-line me-2"></i>
                    Información del Curso
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">{{ $curso->titulo }}</h6>
                        <p class="text-muted mb-2">{{ $curso->descripcion }}</p>
                        <div class="d-flex gap-3">
                            <span class="badge bg-info">
                                <i class="ri-calendar-line me-1"></i>
                                {{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}
                            </span>
                            <span class="badge bg-{{ $curso->estado === 'activo' ? 'success' : 'secondary' }}">
                                {{ ucfirst($curso->estado) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="row text-center">
                            <div class="col-4">
                                <h4 class="text-primary mb-0">{{ $curso->plazas }}</h4>
                                <small class="text-muted">Plazas Totales</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-success mb-0">{{ $curso->getInscritosCount() }}</h4>
                                <small class="text-muted">Inscritos</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-warning mb-0">{{ $curso->getPlazasDisponibles() }}</h4>
                                <small class="text-muted">Disponibles</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Personas disponibles para inscribir -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-user-add-line me-2"></i>
                    Personas Disponibles
                    <span class="badge bg-primary ms-2">{{ $personasDisponibles->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($personasDisponibles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($personasDisponibles as $persona)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="ri-user-line"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $persona->nombre }} {{ $persona->apellido1 }}</strong>
                                                @if($persona->apellido2)
                                                    <br><small class="text-muted">{{ $persona->apellido2 }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($persona->user)
                                            <small class="text-muted">{{ $persona->user->email }}</small>
                                        @else
                                            <span class="badge bg-warning">Sin usuario</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#inscribirModal{{ $persona->id }}">
                                            <i class="ri-user-add-line me-1"></i>
                                            Inscribir
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ri-user-line text-muted fs-1 mb-3"></i>
                        <p class="text-muted">No hay personas disponibles para inscribir.</p>
                        <a href="{{ route('admin.participantes.create') }}" class="btn btn-primary">
                            <i class="ri-user-add-line me-2"></i>
                            Crear Nueva Persona
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Personas ya inscritas -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-team-line me-2"></i>
                    Personas Inscritas
                    <span class="badge bg-success ms-2">{{ $inscritos->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($inscritos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
    <thead>
        <tr>
            <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
                                @foreach($inscritos as $inscrito)
                                @php
                                    $rolId = $inscrito->pivot->rol_participacion_id ?? null;
                                    $rol = $rolesParticipacion->firstWhere('id', $rolId);
                                    $rolClass = $rol && strtolower($rol->nombre) === 'profesor' ? 'bg-primary' : 
                                               ($rol && strtolower($rol->nombre) === 'alumno' ? 'bg-success' : 'bg-secondary');
                                    $estadoClass = $inscrito->pivot->estado === 'activo' ? 'bg-success' : 
                                                  ($inscrito->pivot->estado === 'pendiente' ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="ri-user-line"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $inscrito->nombre }} {{ $inscrito->apellido1 }}</strong>
                                                @if($inscrito->apellido2)
                                                    <br><small class="text-muted">{{ $inscrito->apellido2 }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $rolClass }}">
                                            {{ $rol ? $rol->nombre : 'Sin rol' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $estadoClass }}">
                                            {{ ucfirst($inscrito->pivot->estado ?? 'pendiente') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editarModal{{ $inscrito->id }}">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <form action="{{ route('admin.inscripciones.cursos.baja', [$curso->id, $inscrito->id]) }}" 
                                                  method="POST" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('¿Está seguro de dar de baja a este participante?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="ri-user-unfollow-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.inscripciones.cursos.inscritos', $curso->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="ri-eye-line me-2"></i>
                            Ver detalles completos
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="ri-team-line text-muted fs-1 mb-3"></i>
                        <p class="text-muted">No hay personas inscritas en este curso.</p>
                        <p class="text-muted">Selecciona personas de la lista de la izquierda para inscribirlas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modales para inscribir personas -->
@foreach($personasDisponibles as $persona)
<div class="modal fade" id="inscribirModal{{ $persona->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
                <form action="{{ route('admin.inscripciones.cursos.inscribir', $curso->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="persona_id" value="{{ $persona->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-user-add-line me-2"></i>
                        Inscribir a {{ $persona->nombre }} {{ $persona->apellido1 }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rol_participacion_id" class="form-label">
                            <strong>Rol en el curso:</strong>
                        </label>
                        <select name="rol_participacion_id" id="rol_participacion_id" class="form-select" required>
                            <option value="">Selecciona un rol...</option>
                            @foreach($rolesParticipacion as $rol)
                                <option value="{{ $rol->id }}" 
                                        {{ strtolower($rol->nombre) === 'alumno' ? 'selected' : '' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="ri-information-line me-1"></i>
                            Los alumnos cuentan para las plazas disponibles del curso.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-user-add-line me-1"></i>
                        Inscribir
                    </button>
                </div>
                </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modales para editar inscripciones -->
@foreach($inscritos as $inscrito)
<div class="modal fade" id="editarModal{{ $inscrito->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.inscripciones.cursos.estado', [$curso->id, $inscrito->id]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-edit-line me-2"></i>
                        Editar inscripción de {{ $inscrito->nombre }} {{ $inscrito->apellido1 }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="estado" class="form-label">
                            <strong>Estado de la inscripción:</strong>
                        </label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="activo" {{ ($inscrito->pivot->estado ?? '') === 'activo' ? 'selected' : '' }}>
                                Activo
                            </option>
                            <option value="pendiente" {{ ($inscrito->pivot->estado ?? '') === 'pendiente' ? 'selected' : '' }}>
                                Pendiente
                            </option>
                            <option value="inactivo" {{ ($inscrito->pivot->estado ?? '') === 'inactivo' ? 'selected' : '' }}>
                                Inactivo
                            </option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('css')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}
</style>
@endpush