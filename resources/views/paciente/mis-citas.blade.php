@extends('template.base-paciente')

@section('title', 'Mis Citas - Paciente')
@section('title-page', 'Mis Citas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="ri-calendar-check-line me-2"></i>
                    Mis Citas Médicas
                </h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitarCitaModal">
                    <i class="ri-calendar-add-line me-2"></i>
                    Nueva Cita
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Filtrar por estado</label>
                            <select class="form-select" id="filtroEstado">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendientes</option>
                                <option value="confirmada">Confirmadas</option>
                                <option value="cancelada">Canceladas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ordenar por</label>
                            <select class="form-select" id="ordenarPor">
                                <option value="fecha_desc">Fecha (más reciente)</option>
                                <option value="fecha_asc">Fecha (más antigua)</option>
                                <option value="estado">Estado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Citas -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($citas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Especialidad</th>
                                        <th>Médico</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($citas as $cita)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($cita->fecha_propuesta)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($cita->fecha_propuesta)->format('H:i') }}</td>
                                            <td>{{ $cita->especialidad->nombre ?? 'No especificada' }}</td>
                                            <td>{{ $cita->facultativo->user->name ?? 'No asignado' }}</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $cita->motivo }}">
                                                    {{ $cita->motivo }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($cita->estado === 'pendiente')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @elseif($cita->estado === 'confirmada')
                                                    <span class="badge bg-success">Confirmada</span>
                                                @elseif($cita->estado === 'cancelada')
                                                    <span class="badge bg-danger">Cancelada</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($cita->estado) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="verDetalles({{ $cita->id }})">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                @if($cita->estado === 'pendiente')
                                                    <button class="btn btn-sm btn-outline-danger" onclick="cancelarCita({{ $cita->id }})">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-calendar-line text-muted fs-1"></i>
                            <h5 class="text-muted mt-3">No tienes citas registradas</h5>
                            <p class="text-muted">Solicita tu primera cita médica</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitarCitaModal">
                                <i class="ri-calendar-add-line me-2"></i>
                                Solicitar Cita
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Solicitar Cita -->
@include('paciente.partials.solicitar-cita-modal')

<script>
// Función para ver detalles de la cita
function verDetalles(citaId) {
    // Aquí puedes implementar la lógica para mostrar detalles
    alert('Funcionalidad de detalles próximamente disponible');
}

// Función para cancelar cita
function cancelarCita(citaId) {
    if (confirm('¿Estás seguro de que quieres cancelar esta cita?')) {
        // Aquí puedes implementar la lógica para cancelar la cita
        alert('Funcionalidad de cancelación próximamente disponible');
    }
}

// Filtros
document.getElementById('filtroEstado').addEventListener('change', function() {
    // Implementar filtrado
    console.log('Filtrar por estado:', this.value);
});

document.getElementById('ordenarPor').addEventListener('change', function() {
    // Implementar ordenamiento
    console.log('Ordenar por:', this.value);
});
</script>
@endsection 