@extends('template.base')

@section('title', 'Inscritos - ' . $curso->titulo)
@section('title-sidebar', 'Gestión de Cursos')
@section('title-page', 'Personas Inscritas en Curso')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.cursos.index') }}">Cursos</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.cursos.show', $curso->id) }}">{{ $curso->titulo }}</a>
    </li>
    <li class="breadcrumb-item active">Inscritos</li>
@endsection

@section('content')
<div class="row">
    <!-- Información del curso -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri-book-open-line me-2"></i>
                    {{ $curso->titulo }}
                </h5>
                <div>
                    <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
                       class="btn btn-success btn-sm">
                        <i class="ri-user-add-line me-2"></i>
                        Inscribir más personas
                    </a>
                    <a href="{{ route('admin.cursos.show', $curso->id) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="ri-arrow-left-line me-2"></i>
                        Volver al curso
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-muted mb-3">{{ $curso->descripcion }}</p>
                        <div class="d-flex gap-3 flex-wrap">
                            <span class="badge bg-info">
                                <i class="ri-calendar-line me-1"></i>
                                {{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}
                            </span>
                            <span class="badge bg-{{ $curso->estado === 'activo' ? 'success' : 'secondary' }}">
                                {{ ucfirst($curso->estado) }}
                            </span>
                            @if($curso->precio)
                                <span class="badge bg-warning">
                                    <i class="ri-money-dollar-circle-line me-1"></i>
                                    {{ number_format($curso->precio, 2) }}€
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <h4 class="text-primary mb-0">{{ $curso->plazas }}</h4>
                                <small class="text-muted">Plazas</small>
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
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-success progress-width" data-width="{{ $curso->getPorcentajeOcupacion() }}"></div>
                        </div>
                        <small class="text-muted">{{ number_format($curso->getPorcentajeOcupacion(), 1) }}% ocupado</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas por rol -->
    <div class="col-12 mb-4">
        <div class="row">
            @php
                $profesores = $inscritos->filter(function($inscrito) use ($roles) {
                    $rolId = $inscrito->pivot->rol_participacion_id ?? null;
                    $rol = $roles->get($rolId);
                    return $rol && strtolower($rol->nombre) === 'profesor';
                });
                $alumnos = $inscritos->filter(function($inscrito) use ($roles) {
                    $rolId = $inscrito->pivot->rol_participacion_id ?? null;
                    $rol = $roles->get($rolId);
                    return $rol && strtolower($rol->nombre) === 'alumno';
                });
                $otros = $inscritos->filter(function($inscrito) use ($roles) {
                    $rolId = $inscrito->pivot->rol_participacion_id ?? null;
                    $rol = $roles->get($rolId);
                    return !$rol || (strtolower($rol->nombre) !== 'profesor' && strtolower($rol->nombre) !== 'alumno');
                });
            @endphp
            
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="ri-user-star-line fs-1 mb-2"></i>
                        <h3 class="mb-1">{{ $profesores->count() }}</h3>
                        <p class="mb-0">Profesores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="ri-user-line fs-1 mb-2"></i>
                        <h3 class="mb-1">{{ $alumnos->count() }}</h3>
                        <p class="mb-0">Alumnos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <i class="ri-user-settings-line fs-1 mb-2"></i>
                        <h3 class="mb-1">{{ $otros->count() }}</h3>
                        <p class="mb-0">Otros</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="ri-team-line fs-1 mb-2"></i>
                        <h3 class="mb-1">{{ $inscritos->count() }}</h3>
                        <p class="mb-0">Total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de inscritos -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-team-line me-2"></i>
                    Lista de Personas Inscritas
                    <span class="badge bg-primary ms-2">{{ $inscritos->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($inscritos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
        <thead>
            <tr>
                                    <th>Persona</th>
                                    <th>Contacto</th>
                <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Fecha de Inscripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
                                @foreach($inscritos as $inscrito)
                                @php
                                    $rolId = $inscrito->pivot->rol_participacion_id ?? null;
                                    $rol = $roles->get($rolId);
                                    $rolClass = $rol && strtolower($rol->nombre) === 'profesor' ? 'bg-primary' : 
                                               ($rol && strtolower($rol->nombre) === 'alumno' ? 'bg-success' : 'bg-secondary');
                                    $estadoClass = $inscrito->pivot->estado === 'activo' ? 'bg-success' : 
                                                  ($inscrito->pivot->estado === 'pendiente' ? 'bg-warning' : 'bg-danger');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="ri-user-line"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $inscrito->nombre }} {{ $inscrito->apellido1 }}</strong>
                                                @if($inscrito->apellido2)
                                                    <br><small class="text-muted">{{ $inscrito->apellido2 }}</small>
                                                @endif
                                                @if($inscrito->dni)
                                                    <br><small class="text-muted">DNI: {{ $inscrito->dni }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($inscrito->user)
                                            <div>
                                                <i class="ri-mail-line me-1"></i>
                                                <small>{{ $inscrito->user->email }}</small>
                                            </div>
                                        @endif
                                        @if($inscrito->tfno)
                                            <div>
                                                <i class="ri-phone-line me-1"></i>
                                                <small>{{ $inscrito->tfno }}</small>
                                            </div>
                                        @endif
                                        @if(!$inscrito->user && !$inscrito->tfno)
                                            <span class="badge bg-warning">Sin contacto</span>
                                        @endif
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
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($inscrito->pivot->created_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editarModal{{ $inscrito->id }}"
                                                    title="Editar inscripción">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <a href="{{ route('admin.participantes.show', $inscrito->id) }}" 
                                               class="btn btn-outline-info"
                                               title="Ver perfil">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <form action="{{ route('admin.inscripciones.cursos.baja', [$curso->id, $inscrito->id]) }}" 
                                                  method="POST" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('¿Está seguro de dar de baja a {{ $inscrito->nombre }} {{ $inscrito->apellido1 }}?')">
                            @csrf
                            @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger"
                                                        title="Dar de baja">
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
                @else
                    <div class="text-center py-5">
                        <i class="ri-team-line text-muted fs-1 mb-3"></i>
                        <h5 class="text-muted">No hay personas inscritas</h5>
                        <p class="text-muted">Este curso aún no tiene participantes inscritos.</p>
                        <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
                           class="btn btn-primary">
                            <i class="ri-user-add-line me-2"></i>
                            Inscribir primera persona
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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
                        <label class="form-label">
                            <strong>Persona:</strong>
                        </label>
                        <p class="form-control-plaintext">
                            {{ $inscrito->nombre }} {{ $inscrito->apellido1 }} {{ $inscrito->apellido2 }}
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Rol actual:</strong>
                        </label>
                        @php
                            $rolId = $inscrito->pivot->rol_participacion_id ?? null;
                            $rol = $roles->get($rolId);
                        @endphp
                        <p class="form-control-plaintext">
                            <span class="badge {{ $rolClass }}">
                                {{ $rol ? $rol->nombre : 'Sin rol' }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">
                            <strong>Estado de la inscripción:</strong>
                        </label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="activo" {{ ($inscrito->pivot->estado ?? '') === 'activo' ? 'selected' : '' }}>
                                Activo - Participa normalmente
                            </option>
                            <option value="pendiente" {{ ($inscrito->pivot->estado ?? '') === 'pendiente' ? 'selected' : '' }}>
                                Pendiente - Esperando confirmación
                            </option>
                            <option value="inactivo" {{ ($inscrito->pivot->estado ?? '') === 'inactivo' ? 'selected' : '' }}>
                                Inactivo - No participa temporalmente
                            </option>
                        </select>
                        <div class="form-text">
                            <i class="ri-information-line me-1"></i>
                            Solo los participantes con estado "activo" pueden acceder al contenido del curso.
                        </div>
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
    width: 40px;
    height: 40px;
    font-size: 16px;
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-width');
    progressBars.forEach(function(bar) {
        const width = bar.getAttribute('data-width');
        bar.style.width = width + '%';
    });
});
</script>
@endpush