@extends('template.base')

@section('title', 'Mis Solicitudes de Cita')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Mis Solicitudes de Cita</h4>
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
                                    <th>Profesor</th>
                                    <th>Motivo</th>
                                    <th>Fecha Propuesta</th>
                                    <th>Estado</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                    <tr>
                                        <td>{{ $solicitud->profesor->name }}</td>
                                        <td>{{ $solicitud->motivo }}</td>
                                        <td>{{ \Carbon\Carbon::parse($solicitud->fecha_propuesta)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $solicitud->estado === 'pendiente' ? 'warning' : ($solicitud->estado === 'confirmada' ? 'success' : 'danger') }}">
                                                {{ ucfirst($solicitud->estado) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
    </div>
</div>
@endsection