@extends('template.base-paciente')

@section('title', 'Solicitar Cita - Paciente')
@section('title-page', 'Solicitar Cita')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <h2 class="text-primary mb-3">
                        <i class="ri-calendar-add-line me-2"></i>
                        Solicitar Cita Médica
                    </h2>
                    <p class="text-muted mb-4">Completa los pasos para solicitar tu cita médica. El médico revisará tu solicitud y te confirmará.</p>
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#solicitarCitaModal">
                        <i class="ri-calendar-add-line me-2"></i>
                        Comenzar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del proceso -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-stethoscope-line fs-2"></i>
                    </div>
                    <h5>1. Seleccionar Especialidad</h5>
                    <p class="text-muted">Elige la especialidad médica que necesitas</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-heart-pulse-line fs-2"></i>
                    </div>
                    <h5>2. Elegir Prestación</h5>
                    <p class="text-muted">Selecciona el tipo de consulta o procedimiento</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-info text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri-calendar-time-line fs-2"></i>
                    </div>
                    <h5>3. Fecha y Hora</h5>
                    <p class="text-muted">Indica tu fecha y hora preferida</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Especialidades disponibles -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="ri-stethoscope-line me-2"></i>
                        Especialidades Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($especialidades as $especialidad)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $especialidad->nombre }}</h6>
                                        <p class="card-text text-muted">{{ $especialidad->descripcion }}</p>
                                        <button class="btn btn-outline-primary btn-sm" onclick="seleccionarEspecialidad({{ $especialidad->id }})">
                                            <i class="ri-calendar-add-line me-1"></i>
                                            Solicitar Cita
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Solicitar Cita -->
@include('paciente.partials.solicitar-cita-modal')

<script>
function seleccionarEspecialidad(especialidadId) {
    // Abrir el modal y seleccionar la especialidad
    const modal = new bootstrap.Modal(document.getElementById('solicitarCitaModal'));
    modal.show();
    
    // Seleccionar la especialidad en el select
    setTimeout(() => {
        document.getElementById('especialidad_id').value = especialidadId;
        // Trigger change event para cargar las prestaciones
        document.getElementById('especialidad_id').dispatchEvent(new Event('change'));
    }, 500);
}
</script>
@endsection 