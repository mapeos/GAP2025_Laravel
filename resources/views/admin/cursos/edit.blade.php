@extends('template.base')

@section('title', 'Editar Curso')

@section('content')
<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="ri-edit-line me-2 text-primary"></i>
                Editar Curso: {{ $curso->titulo }}
            </h1>
            <p class="text-muted mb-0">Modifique la información del curso según sea necesario</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cursos.show', $curso->id) }}" class="btn btn-outline-info">
                <i class="ri-eye-line me-2"></i>
                Ver Detalles
            </a>
            <a href="{{ route('admin.cursos.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-2"></i>
                Volver a Cursos
            </a>
        </div>
    </div>

    <!-- Mensajes flash -->
    @include('template.partials.alerts')

    <!-- Mensajes de error -->
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="ri-error-warning-line me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">Se encontraron errores en el formulario:</h6>
                    <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.cursos.update', $curso->id) }}" method="POST" enctype="multipart/form-data" id="formEditarCurso">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Columna principal - Información del curso -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-primary">
                            <i class="ri-book-open-line me-2"></i>
                            Información General del Curso
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Título -->
                        <div class="mb-4">
                            <label for="titulo" class="form-label fw-semibold">
                                <i class="ri-book-line me-1 text-primary"></i>
                                Título del Curso <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="titulo" 
                                   id="titulo" 
                                   class="form-control form-control-lg" 
                                   value="{{ $curso->titulo }}" 
                                   placeholder="Ingrese el título del curso"
                                   required>
                            <div class="form-text">
                                <i class="ri-information-line me-1"></i>
                                El título debe ser descriptivo y atractivo para los estudiantes
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-semibold">
                                <i class="ri-file-text-line me-1 text-primary"></i>
                                Descripción del Curso
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      class="form-control" 
                                      rows="5" 
                                      placeholder="Describa el contenido, objetivos y beneficios del curso">{{ $curso->descripcion }}</textarea>
                            <div class="form-text">
                                <i class="ri-information-line me-1"></i>
                                Proporcione una descripción detallada que ayude a los estudiantes a entender qué aprenderán
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="row mb-4">
            <div class="col-md-6">
                                <label for="fechaInicio" class="form-label fw-semibold">
                                    <i class="ri-calendar-line me-1 text-primary"></i>
                                    Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="fechaInicio" 
                                       id="fechaInicio" 
                                       class="form-control" 
                                       value="{{ $curso->fechaInicio }}" 
                                       required>
                                <div class="form-text">
                                    <i class="ri-information-line me-1"></i>
                                    La fecha debe ser futura
        </div>
            </div>
            <div class="col-md-6">
                                <label for="fechaFin" class="form-label fw-semibold">
                                    <i class="ri-calendar-check-line me-1 text-primary"></i>
                                    Fecha de Finalización <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="fechaFin" 
                                       id="fechaFin" 
                                       class="form-control" 
                                       value="{{ $curso->fechaFin }}" 
                                       required>
                                <div class="form-text">
                                    <i class="ri-information-line me-1"></i>
                                    Debe ser igual o posterior a la fecha de inicio
                </div>
            </div>
        </div>

                        <!-- Plazas y Precio -->
                        <div class="row mb-4">
            <div class="col-md-6">
                                <label for="plazas" class="form-label fw-semibold">
                                    <i class="ri-seat-line me-1 text-primary"></i>
                                    Número de Plazas <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="plazas" 
                                       id="plazas" 
                                       class="form-control" 
                                       value="{{ $curso->plazas }}" 
                                       min="1" 
                                       placeholder="Ej: 20"
                                       required>
                                <div class="form-text">
                                    <i class="ri-information-line me-1"></i>
                                    Capacidad máxima de estudiantes
                </div>
            </div>
            <div class="col-md-6">
                                <label for="precio" class="form-label fw-semibold">
                                    <i class="ri-money-euro-circle-line me-1 text-primary"></i>
                                    Precio del Curso
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" 
                                           step="0.01" 
                                           name="precio" 
                                           id="precio" 
                                           class="form-control" 
                                           value="{{ $curso->precio }}" 
                                           min="0" 
                                           placeholder="0.00">
                                </div>
                                <div class="form-text">
                                    <i class="ri-information-line me-1"></i>
                                    Deje en 0 si el curso es gratuito
                </div>
            </div>
        </div>

                        <!-- Estado -->
                        <div class="mb-4">
                            <label for="estado" class="form-label fw-semibold">
                                <i class="ri-toggle-line me-1 text-primary"></i>
                                Estado del Curso <span class="text-danger">*</span>
                            </label>
                            <select name="estado" id="estado" class="form-select" required>
                                <option value="">Seleccione el estado</option>
                                <option value="activo" {{ $curso->estado == 'activo' ? 'selected' : '' }}>
                                    <i class="ri-checkbox-circle-line"></i> Activo
                                </option>
                                <option value="inactivo" {{ $curso->estado == 'inactivo' ? 'selected' : '' }}>
                                    <i class="ri-close-circle-line"></i> Inactivo
                                </option>
            </select>
                            <div class="form-text">
                                <i class="ri-information-line me-1"></i>
                                Los cursos activos son visibles para los estudiantes
                            </div>
                        </div>
                    </div>
                </div>
        </div>

            <!-- Columna lateral - Imagen de portada -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 text-primary">
                            <i class="ri-image-line me-2"></i>
                            Imagen de Portada
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Imagen actual -->
                        @if ($curso->portada_path)
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-success">
                                    <i class="ri-check-circle-line me-1"></i>
                                    Imagen Actual
                                </label>
                                <div class="border rounded p-3 bg-light text-center">
                                    <img src="{{ asset('storage/' . $curso->portada_path) }}" 
                                         alt="Portada actual" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 150px;">
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="ri-image-line me-1"></i>
                                            Portada actual del curso
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Subir nueva imagen -->
        <div class="mb-3">
                            <label for="portada" class="form-label fw-semibold">
                                <i class="ri-upload-line me-1 text-primary"></i>
            @if ($curso->portada_path)
                                    Cambiar Imagen
                                @else
                                    Seleccionar Imagen
                                @endif
                            </label>
                            <input type="file" 
                                   name="portada" 
                                   id="portada" 
                                   class="form-control" 
                                   accept="image/*">
                            <div class="form-text">
                                <i class="ri-information-line me-1"></i>
                                Formatos: JPG, PNG, WEBP (máximo 10MB)
                            </div>
                        </div>
                        
                        <!-- Vista previa de la nueva imagen -->
                        <div id="preview-container" class="text-center" style="display: none;">
                            <div class="border rounded p-3 bg-light">
                                <img id="image-preview" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="ri-eye-line me-1"></i>
                                        Vista previa de nueva imagen
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="alert alert-info border-0 mt-3">
                            <div class="d-flex align-items-start">
                                <i class="ri-lightbulb-line me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Recomendaciones:</small>
                                    <ul class="mb-0 mt-1 small">
                                        <li>Use imágenes de alta calidad (mínimo 800x600px)</li>
                                        <li>Formato horizontal para mejor visualización</li>
                                        <li>Evite texto en la imagen</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </div>
    </form>

    <!-- Sección de gestión de temario -->
    <div class="card shadow-sm border-0 mb-4" id="temario">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 text-primary">
                <i class="ri-file-text-line me-2"></i>
                Gestión de Temario
            </h5>
        </div>
        <div class="card-body p-4">
            @if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
                <!-- Temario existente -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="alert alert-success border-0">
                            <div class="d-flex align-items-center">
                                <i class="ri-check-circle-line me-3 fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Temario disponible</h6>
                                    <p class="mb-2">El curso ya tiene un temario subido y disponible para los estudiantes.</p>
                                    <div class="d-flex gap-2">
                                        <a href="{{ asset('storage/' . $curso->temario_path) }}" 
                                           target="_blank" 
                                           class="btn btn-info btn-sm">
                                            <i class="ri-eye-line me-2"></i>
                                            Ver/Descargar Temario
                                        </a>
                                        <form action="{{ route('admin.cursos.delete-temario', $curso->id) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              id="formEliminarTemario">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" id="btnEliminarTemario">
                                                <span class="btn-text">
                                                    <i class="ri-delete-bin-line me-2"></i>
                                                    Eliminar Temario
                                                </span>
                                                <span class="btn-spinner" style="display: none;">
                                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                    Eliminando...
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                <div class="mb-3">
                                <i class="ri-file-pdf-line text-success fs-1"></i>
                            </div>
                            <h6 class="text-success mb-2">Temario Activo</h6>
                            <small class="text-muted">
                                <i class="ri-time-line me-1"></i>
                                Subido el {{ \Carbon\Carbon::parse(Storage::disk('public')->lastModified($curso->temario_path))->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Formulario para cambiar temario -->
                <div class="border rounded p-4 bg-light">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-8">
                            <h6 class="mb-0 text-primary">
                                <i class="ri-upload-line me-2"></i>
                                Cambiar Temario
                            </h6>
                            <small class="text-muted">Sube un nuevo archivo para reemplazar el temario actual</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-warning text-dark">
                                <i class="ri-alert-line me-1"></i>
                                Reemplazará el actual
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('admin.cursos.upload', $curso->id) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          id="formTemarioEdit">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="temario" class="form-label fw-semibold">
                                    <i class="ri-file-upload-line me-1 text-primary"></i>
                                    Seleccionar Nuevo Temario
                                </label>
                                <input type="file" 
                                       name="temario" 
                                       id="temario" 
                                       class="form-control" 
                                       accept=".pdf,.doc,.docx" 
                                       required>
                                <div class="form-text">
                                    <i class="ri-information-line me-1"></i>
                                    Formatos: PDF, DOC, DOCX (máximo 25MB)
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-warning w-100" id="btnSubirTemarioEdit">
                                    <span class="btn-text">
                                        <i class="ri-upload-line me-2"></i>
                                        Cambiar Temario
                                    </span>
                                    <span class="btn-spinner" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Subiendo...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <!-- Sin temario -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="alert alert-warning border-0">
                            <div class="d-flex align-items-center">
                                <i class="ri-alert-line me-3 fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Sin temario</h6>
                                    <p class="mb-2">Este curso aún no tiene un temario subido. Los estudiantes no podrán acceder al contenido del curso.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="ri-file-text-line text-muted fs-1"></i>
                            </div>
                            <h6 class="text-muted mb-2">Sin Temario</h6>
                            <small class="text-muted">No hay archivo subido</small>
                        </div>
                    </div>
                </div>

                <!-- Formulario para subir temario -->
                <div class="border rounded p-4 bg-light">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-8">
                            <h6 class="mb-0 text-primary">
                                <i class="ri-upload-line me-2"></i>
                                Subir Temario
                            </h6>
                            <small class="text-muted">Agrega el contenido del curso para que los estudiantes puedan acceder</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-success">
                                <i class="ri-add-line me-1"></i>
                                Nuevo archivo
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('admin.cursos.upload', $curso->id) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          id="formTemarioEdit">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="temario" class="form-label fw-semibold">
                                    <i class="ri-file-upload-line me-1 text-primary"></i>
                                    Seleccionar Temario
                                </label>
                                <input type="file" 
                                       name="temario" 
                                       id="temario" 
                                       class="form-control" 
                                       accept=".pdf,.doc,.docx" 
                                       required>
                                <div class="form-text">
                                    <i class="ri-information-line me-1"></i>
                                    Formatos: PDF, DOC, DOCX (máximo 25MB)
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100" id="btnSubirTemarioEdit">
                                    <span class="btn-text">
                                        <i class="ri-upload-line me-2"></i>
                                        Subir Temario
                                    </span>
                                    <span class="btn-spinner" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Subiendo...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Información adicional sobre temarios -->
            <div class="alert alert-info border-0 mt-4">
                <div class="d-flex align-items-start">
                    <i class="ri-lightbulb-line me-2 mt-1"></i>
                    <div>
                        <small class="fw-semibold">Información sobre temarios:</small>
                        <ul class="mb-0 mt-1 small">
                            <li>Los estudiantes podrán descargar el temario una vez subido</li>
                            <li>Se recomienda usar formato PDF para mejor compatibilidad</li>
                            <li>El archivo debe contener toda la información del curso</li>
                            <li>Máximo 25MB por archivo</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción finales -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        <i class="ri-information-line me-1"></i>
                        Los campos marcados con <span class="text-danger">*</span> son obligatorios
                    </small>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.cursos.show', $curso->id) }}" class="btn btn-outline-secondary">
                        <i class="ri-close-line me-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" form="formEditarCurso" class="btn btn-primary" id="btnGuardarCambios">
                        <span class="btn-text">
                            <i class="ri-save-line me-2"></i>
                            Guardar Cambios
                        </span>
                        <span class="btn-spinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Guardando cambios...
                        </span>
                </button>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    // Establecer fecha mínima como hoy para evitar fechas pasadas
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('fechaInicio').min = today;
    document.getElementById('fechaFin').min = today;

    // Validación de fechas en tiempo real
    document.getElementById('fechaInicio').addEventListener('change', function() {
        const fechaInicio = this.value;
        const fechaFinInput = document.getElementById('fechaFin');
        
        if (fechaInicio) {
            fechaFinInput.min = fechaInicio;
            
            if (fechaFinInput.value && fechaFinInput.value < fechaInicio) {
                fechaFinInput.value = '';
            }
        }
    });

    document.getElementById('fechaFin').addEventListener('change', function() {
        const fechaFin = this.value;
        const fechaInicio = document.getElementById('fechaInicio').value;
        
        if (fechaInicio && fechaFin < fechaInicio) {
            alert('La fecha de fin debe ser igual o posterior a la fecha de inicio.');
            this.value = '';
        }
    });

    // Validación del formulario antes de enviar
    document.getElementById('formEditarCurso').addEventListener('submit', function(e) {
        const fechaInicio = document.getElementById('fechaInicio').value;
        const fechaFin = document.getElementById('fechaFin').value;
        const today = new Date().toISOString().split('T')[0];
        
        if (fechaInicio < today) {
            e.preventDefault();
            alert('La fecha de inicio no puede ser anterior a hoy.');
            return false;
        }
        
        if (fechaFin < fechaInicio) {
            e.preventDefault();
            alert('La fecha de fin debe ser igual o posterior a la fecha de inicio.');
            return false;
        }
    });

    // Preview de imagen
    document.getElementById('portada').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('preview-container');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            container.style.display = 'none';
        }
    });

    // Spinner para el formulario principal
    document.getElementById('formEditarCurso').addEventListener('submit', function(e) {
        const btn = document.getElementById('btnGuardarCambios');
        const btnText = btn.querySelector('.btn-text');
        const btnSpinner = btn.querySelector('.btn-spinner');
        
        btnText.style.display = 'none';
        btnSpinner.style.display = 'inline-flex';
        btn.disabled = true;
    });

    // Spinner para subida de temario
    const formTemarioEdit = document.getElementById('formTemarioEdit');
    if (formTemarioEdit) {
        formTemarioEdit.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubirTemarioEdit');
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.btn-spinner');
            
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-flex';
            btn.disabled = true;
        });
    }

    // Spinner para eliminación de temario con confirmación
    const formEliminarTemario = document.getElementById('formEliminarTemario');
    if (formEliminarTemario) {
        formEliminarTemario.addEventListener('submit', function(e) {
            // Mostrar confirmación personalizada
            const confirmacion = confirm('¿Estás seguro de que quieres eliminar el temario? Esta acción no se puede deshacer.');
            
            if (!confirmacion) {
                e.preventDefault(); // Cancelar el envío del formulario
                return false;
            }
            
            // Si se confirma, mostrar spinner
            const btn = this.querySelector('button[type="submit"]');
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.btn-spinner');
            
            btnText.style.display = 'none';
            btnSpinner.style.display = 'inline-flex';
            btn.disabled = true;
        });
    }
</script>
@endpush
