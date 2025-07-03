@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('content')
<div class="container py-4">
    <a href="/facultativo/cita/new" class="btn btn-outline-success mb-3"><i class="ri-add-line text-lg"></i> Nueva cita</a>
    <h3 class="mb-4 text-success flex items-center gap-2">
        <i class="fas fa-calendar-alt"></i>
        <span>Citas Médicas</span>
    </h3>
    
    <!-- Botón para abrir modal de nueva cita -->
    <button type="button" class="btn btn-outline-success mt-3 mb-3" data-bs-toggle="modal" data-bs-target="#solicitudCitaMedicaModal">
        <i class="ri-add-line text-lg"></i> Nueva Cita Médica
    </button>
    
    <!-- Tabla de todas las citas -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Todas las Citas</h5>
        </div>
        <div class="card-body p-0">
            @if($citas->isEmpty())
                <div class="alert alert-info m-3">No hay citas médicas registradas.</div>
            @else
                <table class="table table-bordered table-striped mb-0 text-center align-middle">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Paciente</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Hora</th>
                            <th class="text-center">Duración</th>
                            <th class="text-center">Especialidad</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($citas as $cita)
                            <tr>
                                <td class="text-center align-middle">{{ $cita->id }}</td>
                                <td class="text-center align-middle">{{ $cita->alumno->name }}</td>
                                <td class="text-center align-middle">{{ $cita->fecha_propuesta->format('d/m/Y') }}</td>
                                <td class="text-center align-middle">{{ $cita->fecha_propuesta->format('H:i') }}</td>
                                <td class="text-center align-middle">
                                    {{ $cita->duracion_minutos ? $cita->duracion_minutos . ' min' : 'N/A' }}
                                </td>
                                <td class="text-center align-middle">
                                    {{ $cita->especialidad ? $cita->especialidad->nombre : 'N/A' }}
                                </td>
                                <td class="text-center align-middle">
                                    @if($cita->estado === 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($cita->estado === 'confirmada')
                                        <span class="badge bg-success">Confirmada</span>
                                    @else
                                        <span class="badge bg-danger">Rechazada</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <a href="/facultativo/cita/{{ $cita->id }}" class="btn btn-primary btn-sm me-1" title="Ver detalles">
                                        <i class="ri-search-line"></i>
                                    </a>
                                    @if($cita->estado === 'pendiente')
                                        <button class="btn btn-success btn-sm me-1" title="Confirmar cita" 
                                                onclick="confirmarCita({{ $cita->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" title="Rechazar cita"
                                                onclick="rechazarCita({{ $cita->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Citas Pendientes -->
    @if($citas->where('estado', 'pendiente')->count() > 0)
        <hr>
        <h3 class="mb-4 text-warning flex items-center gap-2">
            <i class="fas fa-clock"></i>
            <span>Citas pendientes a confirmar ({{ $citas->where('estado', 'pendiente')->count() }})</span>
        </h3>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0 text-center align-middle">
                    <thead class="table-warning">
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Paciente</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Hora</th>
                            <th class="text-center">Motivo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($citas->where('estado', 'pendiente') as $cita)
                            <tr>
                                <td class="text-center align-middle">{{ $cita->id }}</td>
                                <td class="text-center align-middle">{{ $cita->alumno->name }}</td>
                                <td class="text-center align-middle">{{ $cita->fecha_propuesta->format('d/m/Y') }}</td>
                                <td class="text-center align-middle">{{ $cita->fecha_propuesta->format('H:i') }}</td>
                                <td class="text-center align-middle">
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $cita->motivo }}">
                                        {{ $cita->motivo }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <button class="btn btn-success btn-sm me-1" title="Confirmar cita" 
                                            onclick="confirmarCita({{ $cita->id }})">
                                        <i class="fas fa-check"></i> Confirmar
                                    </button>
                                    <button class="btn btn-danger btn-sm me-1" title="Rechazar cita"
                                            onclick="rechazarCita({{ $cita->id }})">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <a href="/facultativo/cita/{{ $cita->id }}" class="btn btn-primary btn-sm" title="Ver detalles">
                                        <i class="ri-search-line"></i> Detalles
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Incluir el modal de solicitud de cita médica -->
@include('facultativo.partials.solicitud-cita-medica-modal')

<script>
function confirmarCita(citaId) {
    if (confirm('¿Estás seguro de que quieres confirmar esta cita?')) {
        // Aquí iría la lógica para confirmar la cita
        window.location.href = `/solicitud-citas/${citaId}/actualizar-estado?estado=confirmada`;
    }
}

function rechazarCita(citaId) {
    if (confirm('¿Estás seguro de que quieres rechazar esta cita?')) {
        // Aquí iría la lógica para rechazar la cita
        window.location.href = `/solicitud-citas/${citaId}/actualizar-estado?estado=rechazada`;
    }
}
</script>
@endsection