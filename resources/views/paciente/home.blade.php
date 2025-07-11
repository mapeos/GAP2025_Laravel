@extends('template.base-paciente')

@section('title', 'Inicio - Paciente')
@section('title-page', 'Inicio')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <h2 class="text-primary mb-3">
                        <i class="ri-heart-pulse-line me-2"></i>
                        Bienvenido, {{ $user->name }}
                    </h2>
                    <p class="text-muted mb-4">Tu salud es nuestra prioridad. Solicita una cita médica cuando lo necesites.</p>
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#solicitarCitaModal">
                        <i class="ri-calendar-add-line me-2"></i>
                        Solicitar Cita Médica
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning text-white rounded-circle p-3">
                                <i class="ri-time-line fs-1"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Citas Pendientes</h6>
                            <h3 class="mb-0 text-warning">{{ $citasPendientes }}</h3>
                            <small class="text-muted">Esperando confirmación</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success text-white rounded-circle p-3">
                                <i class="ri-calendar-check-line fs-1"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Citas Confirmadas</h6>
                            <h3 class="mb-0 text-success">{{ $citasConfirmadas }}</h3>
                            <small class="text-muted">Citas programadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info text-white rounded-circle p-3">
                                <i class="ri-user-heart-line fs-1"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Próximas Citas</h6>
                            <h3 class="mb-0 text-info">{{ $proximasCitas->count() }}</h3>
                            <small class="text-muted">En los próximos días</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Próximas Citas -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="ri-calendar-event-line me-2"></i>
                        Próximas Citas
                    </h5>
                </div>
                <div class="card-body">
                    @if($proximasCitas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Especialidad</th>
                                        <th>Médico</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proximasCitas as $cita)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($cita->fecha_propuesta)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($cita->fecha_propuesta)->format('H:i') }}</td>
                                            <td>{{ $cita->especialidad->nombre ?? 'No especificada' }}</td>
                                            <td>{{ $cita->facultativo->user->name ?? 'No asignado' }}</td>
                                            <td>
                                                <span class="badge bg-success">Confirmada</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-calendar-line text-muted fs-1"></i>
                            <p class="text-muted mt-2">No tienes citas próximas programadas</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitarCitaModal">
                                <i class="ri-calendar-add-line me-2"></i>
                                Solicitar mi primera cita
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
@endsection 