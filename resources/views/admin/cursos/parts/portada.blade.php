{{-- 
    PARTIAL: Portada (Gestión de Portadas)
    =====================================
    
    FUNCIÓN: Gestiona la visualización y subida de portadas del curso
    - Muestra la portada actual si existe (con vista previa)
    - Permite descargar/ver la portada en nueva pestaña
    - Formulario para subir nueva portada
    - Validación de formatos permitidos (JPG, PNG, WEBP)
    - Estado vacío con iconos informativos
    
    VARIABLES REQUERIDAS:
    - $curso: Modelo Curso con campo portada_path
    
    FUNCIONALIDADES:
    - Verificación de existencia del archivo en storage
    - Vista previa de la imagen actual
    - Validación de tipos de archivo en frontend
    - Envío a ruta de upload con CSRF protection
    
    USO: @include('admin.cursos.parts.portada', ['curso' => $curso])
--}}

{{-- Gestión de portada del curso --}}
<div class="card h-100">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>
            <i class="ri-image-line me-2"></i>
            Portada
        </span>
    </div>
    <div class="card-body">
        @if ($curso->portada_path && Storage::disk('public')->exists($curso->portada_path))
            <div class="text-center mb-4">
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $curso->portada_path) }}" 
                         alt="Portada del curso" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-height: 200px; max-width: 100%;">
                </div>
                <div class="d-flex gap-2 justify-content-center mb-2">
                    <a href="{{ asset('storage/' . $curso->portada_path) }}" 
                       target="_blank" 
                       class="btn btn-primary btn-sm shadow-sm">
                        <i class="ri-external-link-line me-2"></i> 
                        Ver Portada Completa
                    </a>
                    <form action="{{ route('admin.cursos.delete-portada', $curso->id) }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar la portada?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm shadow-sm">
                            <i class="ri-delete-bin-line me-2"></i> 
                            Eliminar
                        </button>
                    </form>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="ri-time-line me-1"></i>
                    Subida el {{ \Carbon\Carbon::parse(Storage::disk('public')->lastModified($curso->portada_path))->format('d/m/Y H:i') }}
                </small>
            </div>
        @else
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="ri-image-line text-muted fs-1"></i>
                </div>
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i>
                    No se ha subido ninguna portada.
                </div>
            </div>
        @endif

        <hr>

        <h6 class="mb-3">
            <i class="ri-upload-line me-2"></i>
            Subir nueva portada
        </h6>
        
        <form action="{{ route('admin.cursos.upload-portada', $curso->id) }}" method="POST" enctype="multipart/form-data" id="formPortada">
            @csrf
            <div class="mb-3">
                <label for="portada" class="form-label">Seleccionar imagen</label>
                <input type="file" 
                       name="portada" 
                       id="portada" 
                       class="form-control" 
                       accept="image/*" 
                       required>
                <small class="text-muted">
                    Formatos permitidos: JPG, PNG, GIF (máx. 10MB)
                </small>
            </div>
            <button type="submit" class="btn btn-success w-100" id="btnSubirPortada">
                <span class="btn-text">
                    <i class="ri-upload-line me-2"></i> 
                    Subir portada
                </span>
                <span class="btn-spinner" style="display: none;">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Subiendo...
                </span>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Spinner para subida de portada
    const formPortada = document.getElementById('formPortada');
    if (formPortada) {
        formPortada.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubirPortada');
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.btn-spinner');
            
            // Mostrar spinner
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-flex';
            btn.disabled = true;
        });
    }

    // Spinner para eliminación de portada
    const deletePortadaForms = document.querySelectorAll('form[action*="delete-portada"]');
    deletePortadaForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                // Guardar el texto original
                btn.dataset.originalText = btn.innerHTML;
                
                // Mostrar spinner
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Eliminando...';
                btn.disabled = true;
            }
        });
    });
});
</script> 