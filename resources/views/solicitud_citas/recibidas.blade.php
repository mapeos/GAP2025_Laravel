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
            @if($solicitudes->isEmpty())
                <div class="alert alert-info">No tiene solicitudes de cita pendientes.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Motivo</th>
                                    <th>Fecha Propuesta</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
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
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Confirmar esta cita?')">
                                                        <i class="ri-check-line"></i> Confirmar
                                                    </button>
                                                </form>
                                                <form action="{{ route('solicitud-cita.actualizar-estado', $solicitud) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="estado" value="rechazada">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Rechazar esta cita?')">
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
            
        </div>
    </div>
</div>
@endsection