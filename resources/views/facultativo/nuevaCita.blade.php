@extends('template.base-facultativo')
@section('title', 'Nueva Cita Médica')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Nueva Cita')
@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4 text-success flex items-center gap-2">
                <i class="ri-heart-pulse-line"></i>
                <span>Nueva Cita Médica</span>
            </h3>
            
            <!-- Información sobre el proceso -->
            <div class="alert alert-info">
                <h5><i class="ri-information-line me-2"></i>Proceso de Solicitud de Cita</h5>
                <p class="mb-0">
                    Para solicitar una nueva cita médica, complete el formulario a continuación. 
                    La solicitud será revisada y confirmada por el médico correspondiente.
                </p>
          </div>

            <!-- Botón para abrir modal -->
            <div class="text-center my-5">
                <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#solicitudCitaMedicaModal">
                    <i class="ri-add-line me-2"></i>
                    Solicitar Nueva Cita Médica
                </button>
          </div>

            <!-- Información adicional -->
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="ri-user-line text-success fs-1 mb-3"></i>
                            <h5>Seleccionar Paciente</h5>
                            <p class="text-muted">Elija el paciente que requiere atención médica</p>
          </div>
        </div>
      </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="ri-calendar-line text-success fs-1 mb-3"></i>
                            <h5>Agendar Fecha</h5>
                            <p class="text-muted">Seleccione fecha y hora preferida para la consulta</p>
      </div>
          </div>
          </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="ri-heart-pulse-line text-success fs-1 mb-3"></i>
                            <h5>Especificar Motivo</h5>
                            <p class="text-muted">Describa el motivo de la consulta y síntomas</p>
          </div>
          </div>
        </div>
      </div>

            <!-- Información sobre especialidades disponibles -->
            @if(isset($especialidades) && $especialidades->count() > 0)
                <div class="mt-5">
                    <h4 class="text-success mb-3">
                        <i class="ri-stethoscope-line me-2"></i>Especialidades Disponibles
                    </h4>
                    <div class="row">
                        @foreach($especialidades as $especialidad)
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-2">
                                            <span class="badge" style="background-color: {{ $especialidad->color }}; color: white;">
                                                {{ $especialidad->nombre }}
                                            </span>
      </div>
                                        @if($especialidad->descripcion)
                                            <small class="text-muted">{{ $especialidad->descripcion }}</small>
                                        @endif
          </div>
          </div>
        </div>
                        @endforeach
        </div>
      </div>
            @endif

            <!-- Información sobre tratamientos disponibles -->
            @if(isset($tratamientos) && $tratamientos->count() > 0)
                <div class="mt-5">
                    <h4 class="text-success mb-3">
                        <i class="ri-capsule-line me-2"></i>Tratamientos Disponibles
                    </h4>
                    <div class="row">
                        @foreach($tratamientos->take(6) as $tratamiento)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $tratamiento->nombre }}</h6>
                                        @if($tratamiento->descripcion)
                                            <p class="card-text small text-muted">{{ $tratamiento->descripcion }}</p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-info">{{ $tratamiento->duracion_formateada }}</span>
                                            <span class="text-success fw-bold">{{ $tratamiento->costo_formateado }}</span>
          </div>
          </div>
        </div>
      </div>
                        @endforeach
                    </div>
                    @if($tratamientos->count() > 6)
                        <div class="text-center mt-3">
                            <a href="/facultativo/tratamientos" class="btn btn-outline-success">
                                Ver todos los tratamientos
                            </a>
      </div>
                    @endif
      </div>
            @endif
      </div>
      </div>
</div>

<!-- Incluir el modal de solicitud de cita médica -->
@include('facultativo.partials.solicitud-cita-medica-modal')

@endsection