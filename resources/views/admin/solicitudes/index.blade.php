@extends('template.base')

@section('title', 'Solicitudes de Inscripción')
@section('title-page', 'Gestión de Solicitudes de Inscripción')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="ri-user-add-line me-2"></i> Solicitudes de Inscripción</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Alumno</th>
                            <th>Curso</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td>
                                    @if(isset($solicitud->pivot->persona) && $solicitud->pivot->persona)
                                        <a href="{{ route('admin.participantes.show', $solicitud->pivot->persona->id) }}">
                                            {{ $solicitud->pivot->persona->getNombreCompletoAttribute() }}
                                        </a>
                                    @else
                                        <span class="text-muted">Sin datos</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($solicitud->curso) && $solicitud->curso)
                                        <div>
                                            <strong>{{ $solicitud->curso->titulo }}</strong><br>
                                            <small class="text-muted">Fechas: {{ $solicitud->curso->fechaInicio }} - {{ $solicitud->curso->fechaFin }}</small><br>
                                            <small class="text-muted">Plazas: {{ $solicitud->curso->plazas }}</small><br>
                                            <span class="text-muted">{{ $solicitud->curso->descripcion }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Sin datos</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($solicitud->curso) && $solicitud->curso)
                                        {{ $solicitud->curso->precio ? number_format($solicitud->curso->precio, 2) . ' €' : 'Gratuito' }}
                                    @else
                                        <span class="text-muted">Sin datos</span>
                                    @endif
                                </td>
                                <td>
                                    @if($solicitud->pivot->estado == 'activo')
                                        <span class="badge bg-success">Aceptado</span>
                                    @elseif($solicitud->pivot->estado == 'pendiente')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($solicitud->pivot->estado == 'espera')
                                        <span class="badge bg-info">En espera</span>
                                    @elseif($solicitud->pivot->estado == 'rechazado')
                                        <span class="badge bg-danger">Rechazado</span>
                                    @else
                                        <span class="badge bg-secondary">Desconocido</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($solicitud->curso) && $solicitud->curso && isset($solicitud->pivot->persona) && $solicitud->pivot->persona)
                                        <form method="POST" action="{{ route('admin.solicitudes.update', [$solicitud->curso->id, $solicitud->pivot->persona->id]) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="estado" class="form-select form-select-sm d-inline w-auto">
                                                <option value="pendiente" {{ $solicitud->pivot->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                <option value="activo" {{ $solicitud->pivot->estado == 'activo' ? 'selected' : '' }}>Aceptar</option>
                                                <option value="espera" {{ $solicitud->pivot->estado == 'espera' ? 'selected' : '' }}>Lista de espera</option>
                                                <option value="rechazado" {{ $solicitud->pivot->estado == 'rechazado' ? 'selected' : '' }}>Rechazar</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm ms-2">Actualizar</button>
                                        </form>
                                    @else
                                        <span class="text-muted">No disponible</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No hay solicitudes de inscripción pendientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
