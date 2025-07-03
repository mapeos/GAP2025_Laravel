@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Detalles de Cita')
@section('title-sidebar', 'Dashboard Facultativo')
@section('title-page', 'Detalles de Cita')
@section('content')
<div class="container py-4">
    @if(isset($cita))
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Detalles de Cita #{{ $cita->id }}</h5>
                            <div>
                                @if($cita->estado === 'pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($cita->estado === 'confirmada')
                                    <span class="badge bg-success">Confirmada</span>
                                @else
                                    <span class="badge bg-danger">Rechazada</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Información del Paciente</h6>
                                <p><strong>Nombre:</strong> {{ $cita->alumno->name }}</p>
                                <p><strong>Email:</strong> {{ $cita->alumno->email }}</p>
                                
                                <h6 class="text-muted mb-3 mt-4">Información de la Cita</h6>
                                <p><strong>Fecha:</strong> {{ $cita->fecha_propuesta->format('d/m/Y') }}</p>
                                <p><strong>Hora:</strong> {{ $cita->fecha_propuesta->format('H:i') }}</p>
                                <p><strong>Motivo:</strong> {{ $cita->motivo }}</p>
                                @if($cita->sintomas)
                                    <p><strong>Síntomas:</strong> {{ $cita->sintomas }}</p>
                                @endif
                                @if($cita->duracion_minutos)
                                    <p><strong>Duración:</strong> {{ $cita->duracion_minutos }} minutos</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Información Médica</h6>
                                @if($cita->especialidad)
                                    <p><strong>Especialidad:</strong> {{ $cita->especialidad->nombre }}</p>
                                @endif
                                @if($cita->tratamiento)
                                    <p><strong>Tratamiento:</strong> {{ $cita->tratamiento->nombre }}</p>
                                @endif
                                @if($cita->costo)
                                    <p><strong>Costo:</strong> €{{ number_format($cita->costo, 2) }}</p>
                                @endif
                                @if($cita->observaciones_medicas)
                                    <p><strong>Observaciones:</strong> {{ $cita->observaciones_medicas }}</p>
                                @endif
                                @if($cita->fecha_proxima_cita)
                                    <p><strong>Próxima cita:</strong> {{ $cita->fecha_proxima_cita->format('d/m/Y') }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if($cita->estado === 'pendiente')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">Acciones</h6>
                                    <form action="{{ route('facultativo.citas.actualizar-estado', $cita) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="estado" value="confirmada">
                                        <button type="submit" class="btn btn-success me-2" onclick="return confirm('¿Confirmar esta cita?')">
                                            <i class="fas fa-check"></i> Confirmar Cita
                                        </button>
                                    </form>
                                    <form action="{{ route('facultativo.citas.actualizar-estado', $cita) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="estado" value="rechazada">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Rechazar esta cita?')">
                                            <i class="fas fa-times"></i> Rechazar Cita
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h5>No se encontró la cita especificada</h5>
            <p>La cita que buscas no existe o no tienes permisos para verla.</p>
            <a href="{{ route('facultativo.citas') }}" class="btn btn-primary">Volver a Citas</a>
        </div>
    @endif
</div>
@endsection