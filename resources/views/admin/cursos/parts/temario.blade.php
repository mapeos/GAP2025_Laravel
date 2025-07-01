{{-- 
    PARTIAL: Temario (Gestión de Temarios)
    =====================================
    
    FUNCIÓN: Gestiona la visualización y subida de temarios del curso
    - Muestra el temario actual si existe (con fecha de subida)
    - Permite descargar/ver el temario en nueva pestaña
    - Formulario para subir nuevo temario
    - Validación de formatos permitidos (PDF, DOC, DOCX)
    - Estado vacío con iconos informativos
    
    VARIABLES REQUERIDAS:
    - $curso: Modelo Curso con campo temario_path
    
    FUNCIONALIDADES:
    - Verificación de existencia del archivo en storage
    - Formato de fecha de última modificación
    - Validación de tipos de archivo en frontend
    - Envío a ruta de upload con CSRF protection
    
    USO: @include('admin.cursos.parts.temario', ['curso' => $curso])
--}}

{{-- Gestión de temario del curso --}}
<div class="card h-100">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <span>
            <i class="ri-file-text-line me-2"></i>
        Temario
        </span>
    </div>
    <div class="card-body">
        @if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="ri-file-pdf-line text-info fs-1"></i>
                </div>
                <div class="d-flex gap-2 justify-content-center mb-2">
                <a href="{{ asset('storage/' . $curso->temario_path) }}" 
                   target="_blank" 
                       class="btn btn-info btn-sm shadow-sm">
                    <i class="ri-download-line me-2"></i> 
                    Ver/Descargar Temario
            </a>
                    <form action="{{ route('admin.cursos.delete-temario', $curso->id) }}" method="POST" class="d-inline" 
                          id="formEliminarTemarioPartial">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm shadow-sm" id="btnEliminarTemarioPartial">
                            <i class="ri-delete-bin-line me-2"></i> 
                            Eliminar
                        </button>
                    </form>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="ri-time-line me-1"></i>
                    Subido el {{ \Carbon\Carbon::parse(Storage::disk('public')->lastModified($curso->temario_path))->format('d/m/Y H:i') }}
                </small>
            </div>
        @else
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="ri-file-text-line text-muted fs-1"></i>
                </div>
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i>
                    No se ha subido ningún temario.
                </div>
            </div>
        @endif

        <hr>

        <h6 class="mb-3">
            <i class="ri-upload-line me-2"></i>
            Subir nuevo temario
        </h6>
        
        <form action="{{ route('admin.cursos.upload', $curso->id) }}" method="POST" enctype="multipart/form-data" id="formTemario">
            @csrf
            <div class="mb-3">
                <label for="temario" class="form-label">Seleccionar archivo</label>
                <input type="file" 
                       name="temario" 
                       id="temario" 
                       class="form-control" 
                       accept=".pdf,.doc,.docx" 
                       required>
                <small class="text-muted">
                    Formatos permitidos: PDF, DOC, DOCX (máx. 25MB)
                </small>
            </div>
            <button type="submit" class="btn btn-success w-100" id="btnSubirTemario">
                <span class="btn-text">
                <i class="ri-upload-line me-2"></i> 
                Subir temario
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
    // Spinner para subida de temario
    const formTemario = document.getElementById('formTemario');
    if (formTemario) {
        formTemario.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubirTemario');
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.btn-spinner');
            
            // Mostrar spinner
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-flex';
            btn.disabled = true;
        });
    }

    // Spinner para eliminación de temario con confirmación mejorada
    const formEliminarTemarioPartial = document.getElementById('formEliminarTemarioPartial');
    if (formEliminarTemarioPartial) {
        formEliminarTemarioPartial.addEventListener('submit', function(e) {
            // Mostrar confirmación personalizada
            const confirmacion = confirm('¿Estás seguro de que quieres eliminar el temario? Esta acción no se puede deshacer.');
            
            if (!confirmacion) {
                e.preventDefault(); // Cancelar el envío del formulario
                return false;
            }
            
            // Si se confirma, mostrar spinner
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                // Guardar el texto original
                btn.dataset.originalText = btn.innerHTML;
                
                // Mostrar spinner
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Eliminando...';
                btn.disabled = true;
            }
        });
    }
});
</script>