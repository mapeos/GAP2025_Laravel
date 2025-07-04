@extends('template.base')

@section('title', 'Diploma del Curso')

@section('content')
<!-- Spinner de carga -->
<div id="loadingSpinner" class="loading-spinner">
    <div class="spinner-content">
        <div class="spinner"></div>
        <div class="spinner-text">Generando Diploma...</div>
        <button type="button" class="btn btn-outline-secondary btn-sm mt-3" onclick="hideSpinner()">
            <i class="ri-close-line me-1"></i>
            Cancelar
        </button>
    </div>
</div>
<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="ri-award-line me-2 text-primary"></i>
                Diploma del Curso
            </h1>
            <p class="text-muted mb-0">{{ $curso->titulo }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cursos.diploma.full', $curso->id) }}" 
               target="_blank" 
               class="btn btn-info">
                <i class="ri-external-link-line me-2"></i>
                Ver Diploma Completo
            </a>
            <button type="button" 
                    class="btn btn-success"
                    onclick="downloadPDF()">
                <i class="ri-download-line me-2"></i>
                Descargar Diploma
            </button>
            <a href="{{ route('admin.cursos.show', $curso->id) }}" 
               class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-2"></i>
                Volver al Curso
            </a>
        </div>
    </div>

    <!-- Mensajes flash -->
    @include('template.partials.alerts')

    <!-- Información del curso -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="ri-information-line me-2"></i>
                        Información del Curso
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-primary mb-1">{{ $curso->titulo }}</div>
                                <small class="text-muted">Título del Curso</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 text-success mb-1">{{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }}</div>
                                <small class="text-muted">Fecha de Inicio</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h5 text-info mb-1">{{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}</div>
                                <small class="text-muted">Fecha de Finalización</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="badge bg-{{ $curso->estado === 'activo' ? 'success' : 'warning' }} fs-6">
                                    {{ ucfirst($curso->estado) }}
                                </span>
                                <div><small class="text-muted">Estado</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista previa del diploma -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="ri-eye-line me-2"></i>
                        Vista Previa del Diploma
                    </h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="showFront()">
                                <i class="ri-file-text-line me-1"></i>
                                Frente
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="showBack()">
                                <i class="ri-file-text-line me-1"></i>
                                Dorso
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Contenedor del diploma con scroll -->
                    <div class="diploma-preview-container">
                        <div id="diploma-front" class="diploma-view active">
                            @include('admin.cursos.diplomas.template', ['curso' => $curso])
                        </div>
                        <div id="diploma-back" class="diploma-view">
                            @include('admin.cursos.diplomas.template-back', ['curso' => $curso])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles adicionales -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-primary mb-2">Vista Previa</h6>
                                <p class="text-muted small">Visualiza el diploma antes de descargarlo</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-info mb-2">Vista Completa</h6>
                                <p class="text-muted small">Abre el diploma en pantalla completa</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h6 class="text-success mb-2">Descarga PDF</h6>
                                <p class="text-muted small">Descarga el diploma en formato PDF</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Spinner de carga */
.loading-spinner {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.spinner-content {
    background: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-text {
    color: #333;
    font-size: 1.1rem;
    font-weight: 500;
}

.diploma-preview-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 30px;
    min-height: 600px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.diploma-view {
    display: none;
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}

.diploma-view.active {
    display: block;
}

.diploma-view .diploma {
    background: white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border-radius: 15px;
    overflow: hidden;
    transform: scale(0.8);
    transform-origin: center;
    transition: transform 0.3s ease;
}

.diploma-view .diploma:hover {
    transform: scale(0.85);
}

/* Responsive */
@media (max-width: 768px) {
    .diploma-preview-container {
        padding: 15px;
    }
    
    .diploma-view .diploma {
        transform: scale(0.6);
    }
    
    .diploma-view .diploma:hover {
        transform: scale(0.65);
    }
}

@media (max-width: 576px) {
    .diploma-view .diploma {
        transform: scale(0.5);
    }
    
    .diploma-view .diploma:hover {
        transform: scale(0.55);
    }
}
</style>

<script>
// Variable global para la URL de descarga
const downloadUrl = "{{ route('admin.cursos.diploma.download', $curso->id) }}";

function showFront() {
    document.getElementById('diploma-front').classList.add('active');
    document.getElementById('diploma-back').classList.remove('active');
    
    // Actualizar botones
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');
}

function showBack() {
    document.getElementById('diploma-back').classList.add('active');
    document.getElementById('diploma-front').classList.remove('active');
    
    // Actualizar botones
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');
}

// Función para descargar PDF sin alertas
function downloadPDF() {
    // Mostrar spinner de carga
    const spinner = document.getElementById('loadingSpinner');
    spinner.style.display = 'flex';
    
    // Función para ocultar spinner
    function hideSpinner() {
        spinner.style.display = 'none';
    }
    
    // Usar fetch para detectar cuando el PDF está listo
    fetch(downloadUrl)
        .then(response => {
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Error al generar el PDF');
        })
        .then(blob => {
            // Crear URL del blob
            const url = window.URL.createObjectURL(blob);
            
            // Crear enlace de descarga
            const link = document.createElement('a');
            link.href = url;
            link.download = 'diploma_{{ $curso->id }}.pdf';
            
            // Descargar el archivo
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Limpiar URL del blob
            window.URL.revokeObjectURL(url);
            
            // Ocultar spinner después de que se inicie la descarga
            setTimeout(hideSpinner, 1500);
        })
        .catch(error => {
            console.error('Error:', error);
            hideSpinner();
            alert('Error al generar el PDF. Inténtelo de nuevo.');
        });
    
    // Respaldo: ocultar spinner después de 15 segundos máximo
    setTimeout(function() {
        if (spinner.style.display === 'flex') {
            hideSpinner();
        }
    }, 15000);
}

// Función para ocultar spinner manualmente (por si acaso)
function hideSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    spinner.style.display = 'none';
}
</script>
@endsection 