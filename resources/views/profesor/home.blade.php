@extends('template.base')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Panel de Control - Profesor</h1>
            <p class="text-muted">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="ri-calendar-check-line text-primary fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Citas Pendientes</h6>
                            <h3 class="mb-0">{{ $solicitudesPendientes ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="ri-calendar-event-line text-success fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Eventos del Mes</h6>
                            <h3 class="mb-0">{{ $eventosMes ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="ri-user-line text-info fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Estudiantes</h6>
                            <h3 class="mb-0">{{ $totalEstudiantes ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('solicitud-cita.recibidas') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="ri-mail-line me-2"></i>
                                Ver Solicitudes de Cita
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('events.calendar') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="ri-calendar-line me-2"></i>
                                Ver Calendario
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="btn btn-outline-info w-100 py-3">
                                <i class="ri-file-list-line me-2"></i>
                                Ver Reportes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Próximas citas -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Próximas Citas</h5>
                </div>
                <div class="card-body">
                    @if(isset($proximasCitas) && $proximasCitas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Motivo</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proximasCitas as $cita)
                                        <tr>
                                            <td>{{ $cita->alumno->name }}</td>
                                            <td>{{ $cita->motivo }}</td>
                                            <td>{{ $cita->fecha_propuesta->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $cita->estado === 'pendiente' ? 'warning' : ($cita->estado === 'confirmada' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($cita->estado) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-calendar-check-line text-muted fs-1"></i>
                            <p class="text-muted mt-2">No hay citas programadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Actividad Reciente</h5>
                </div>
                <div class="card-body">
                    @if(isset($actividadReciente) && $actividadReciente->count() > 0)
                        <div class="timeline">
                            @foreach($actividadReciente as $actividad)
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-0">{{ $actividad->titulo }}</h6>
                                        <small class="text-muted">{{ $actividad->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ri-time-line text-muted fs-1"></i>
                            <p class="text-muted mt-2">No hay actividad reciente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #0d6efd;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #0d6efd;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 12px;
    height: calc(100% - 12px);
    width: 2px;
    background: #e9ecef;
}
</style>
@endsection
