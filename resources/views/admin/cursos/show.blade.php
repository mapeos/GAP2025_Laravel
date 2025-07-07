@extends('template.base')

@inject('storage', 'Illuminate\Support\Facades\Storage')

@section('title', 'Detalles del Curso')

@section('content')
<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="ri-eye-line me-2 text-primary"></i>
                Detalles del Curso
            </h1>
            <p class="text-muted mb-0">Información completa y estado del curso</p>
</div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cursos.edit', $curso->id) }}" class="btn btn-primary">
                <i class="ri-edit-line me-2"></i>
                Editar Curso
            </a>
            <a href="{{ route('admin.cursos.diploma', $curso->id) }}" class="btn btn-success">
                <i class="ri-award-line me-2"></i>
                Generar Diploma
            </a>
            <a href="{{ route('admin.cursos.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-2"></i>
                Volver a Cursos
            </a>
</div>
    </div>

    <!-- Mensajes flash -->
    @include('template.partials.alerts')

    <!-- Alertas de estado -->
    @include('admin.cursos.parts.status-alerts', ['curso' => $curso])

    <!-- Estadísticas del curso -->
    @include('admin.cursos.parts.course-stats', ['curso' => $curso])

    <div class="row">
        <!-- Información principal del curso -->
        <div class="col-lg-8">
            <!-- Detalles completos del curso -->
            @include('admin.cursos.parts.course-details', ['curso' => $curso])
            
            <!-- Participantes -->
            @include('admin.cursos.parts.participante', ['curso' => $curso])
        </div>
        
        <!-- Panel lateral - Recursos del curso -->
        <div class="col-lg-4">
            <!-- Temario -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ri-file-text-line me-2"></i>
                        Temario del Curso
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="ri-file-pdf-line text-info fs-1"></i>
                            </div>
                            <h6 class="text-success mb-3">
                                <i class="ri-check-circle-line me-2"></i>
                                Temario Disponible
                            </h6>
                            <a href="{{ asset('storage/' . $curso->temario_path) }}" 
                               target="_blank" 
                               class="btn btn-info w-100 mb-3">
                                <i class="ri-download-line me-2"></i> 
                                Ver/Descargar Temario
                            </a>
                            <div class="text-muted small">
                                <i class="ri-time-line me-1"></i>
                                Subido el {{ \Carbon\Carbon::parse(Storage::disk('public')->lastModified($curso->temario_path))->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="ri-file-text-line text-muted fs-1"></i>
                            </div>
                            <div class="alert alert-warning border-0">
                                <i class="ri-alert-line me-2"></i>
                                <strong>Sin temario</strong><br>
                                <small>Este curso aún no tiene un temario subido.</small>
                            </div>
                            <a href="{{ route('admin.cursos.edit', $curso->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ri-edit-line me-2"></i>
                                Agregar Temario
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Portada -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ri-image-line me-2"></i>
                        Imagen de Portada
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if ($curso->portada_path && Storage::disk('public')->exists($curso->portada_path))
                        <div class="text-center">
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $curso->portada_path) }}" 
                                     alt="Portada del curso" 
                                     class="img-fluid rounded shadow-sm" 
                                     style="max-height: 200px; max-width: 100%;">
                            </div>
                            <h6 class="text-success mb-3">
                                <i class="ri-check-circle-line me-2"></i>
                                Portada Disponible
                            </h6>
                            <a href="{{ asset('storage/' . $curso->portada_path) }}" 
                               target="_blank" 
                               class="btn btn-primary w-100 mb-3">
                                <i class="ri-external-link-line me-2"></i> 
                                Ver Portada Completa
                            </a>
                            <div class="text-muted small">
                                <i class="ri-time-line me-1"></i>
                                Subida el {{ \Carbon\Carbon::parse(Storage::disk('public')->lastModified($curso->portada_path))->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="ri-image-line text-muted fs-1"></i>
                            </div>
                            <div class="alert alert-warning border-0">
                                <i class="ri-alert-line me-2"></i>
                                <strong>Sin portada</strong><br>
                                <small>Este curso aún no tiene una imagen de portada.</small>
                            </div>
                            <a href="{{ route('admin.cursos.edit', $curso->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ri-edit-line me-2"></i>
                                Agregar Portada
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


@endsection