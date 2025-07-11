@extends('template.base')

@section('title', 'Solicitudes de Cita Recibidas')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Solicitudes de Cita Recibidas</h4>
            <a href="{{ route('calendario') }}" class="btn btn-secondary">Volver al Calendario</a>
        </div>
        <div class="card-body">
            <!-- Solicitudes Académicas -->
            @if($solicitudesAcademicas->isNotEmpty())
                <h5 class="mb-3">
                    <i class="ri-book-line me-2"></i>
                    Solicitudes Académicas
                </h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Alumno</th>
                                <th>Motivo</th>
                                <th>Fecha Propuesta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudesAcademicas as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->alumno->name }}</td>
                                    <td>{{ $solicitud->motivo }}</td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fecha_propuesta)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $solicitud->estado === 'pendiente' ? 'warning' : ($solicitud->estado === 'confirmada' ? 'success' : 'danger') }}">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($solicitud->estado === 'pendiente')
                                            <form action="{{ route('solicitud-cita.actualizar-estado', $solicitud) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="confirmada">
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Confirmar esta cita académica?')">
                                                    <i class="ri-check-line"></i> Confirmar
                                                </button>
                                            </form>
                                            <form action="{{ route('solicitud-cita.actualizar-estado', $solicitud) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="rechazada">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Rechazar esta cita académica?')">
                                                    <i class="ri-close-line"></i> Rechazar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Solicitudes Médicas -->
            @if($solicitudesMedicas->isNotEmpty())
                <h5 class="mb-3">
                    <i class="ri-heart-pulse-line me-2"></i>
                    Solicitudes Médicas
                </h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Paciente</th>
                                <th>Motivo</th>
                                <th>Síntomas</th>
                                <th>Especialidad</th>
                                <th>Fecha Propuesta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudesMedicas as $solicitud)
                                <tr>
                                    <td>{{ $solicitud->alumno->name }}</td>
                                    <td>{{ $solicitud->motivo }}</td>
                                    <td>
                                        @if($solicitud->sintomas)
                                            <span class="text-muted small">{{ Str::limit($solicitud->sintomas, 50) }}</span>
                                        @else
                                            <span class="text-muted">No especificado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($solicitud->especialidad)
                                            <span class="badge bg-info">{{ $solicitud->especialidad->nombre }}</span>
                                        @else
                                            <span class="text-muted">No especificada</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud->fecha_propuesta)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $solicitud->estado === 'pendiente' ? 'warning' : ($solicitud->estado === 'confirmada' ? 'success' : 'danger') }}">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($solicitud->estado === 'pendiente')
                                            <form action="{{ route('solicitud-cita.actualizar-estado', $solicitud) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="confirmada">
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Confirmar esta cita médica?')">
                                                    <i class="ri-check-line"></i> Confirmar
                                                </button>
                                            </form>
                                            <form action="{{ route('solicitud-cita.actualizar-estado', $solicitud) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="estado" value="rechazada">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Rechazar esta cita médica?')">
                                                    <i class="ri-close-line"></i> Rechazar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Mensaje cuando no hay solicitudes -->
            @if($solicitudesAcademicas->isEmpty() && $solicitudesMedicas->isEmpty())
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    No tienes solicitudes de cita pendientes.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection