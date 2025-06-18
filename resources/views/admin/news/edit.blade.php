@extends('template.base')

@section('title', 'Editar Noticia')
@section('title-sidebar', 'Noticias')
@section('title-page', 'Editar Noticia')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">News</a></li>
<li class="breadcrumb-item active">Edit News</li>
@endsection

@section('content')
<div class="container">
    <h1>Editar Noticia</h1>

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    @include('template.partials.alerts')

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" id="newsForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo', $news->titulo) }}" required>
            @error('titulo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="contenido">Contenido</label>
            <textarea class="form-control @error('contenido') is-invalid @enderror" id="contenido" name="contenido" rows="5" required>{{ old('contenido', $news->contenido) }}</textarea>
            @error('contenido')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="autor">Autor</label>
            <input type="number" class="form-control @error('autor') is-invalid @enderror" id="autor" name="autor" value="{{ old('autor', $news->autor) }}">
            @error('autor')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="fecha_publicacion">Fecha de Publicación</label>
            <div class="input-group">
                <input type="datetime-local" class="form-control @error('fecha_publicacion') is-invalid @enderror" id="fecha_publicacion" name="fecha_publicacion"
                    value="{{ old('fecha_publicacion', optional($news->fecha_publicacion)->format('Y-m-d\TH:i')) }}" required>
                <button type="button" class="btn btn-outline-secondary" id="fechaActual">
                    <i class="ri-time-line"></i> Fecha Actual
                </button>
                @error('fecha_publicacion')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="categorias">Categorías</label>
            <div>
                @foreach($categorias as $categoria)
                <div class="form-check">
                    <input id="categoria_{{ $categoria->id }}" class="form-check-input" type="checkbox" name="categorias[]" value="{{ $categoria->id }}"
                        @if(in_array($categoria->id, old('categorias', $news->categorias->pluck('id')->toArray()))) checked @endif>
                    <label class="form-check-label" for="categoria_{{ $categoria->id }}">
                        {{ $categoria->nombre }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="imagen">Imagen</label>
            <div class="image-upload-container">
                <div class="image-upload-box" id="imageUploadBox">
                    <input type="file" class="image-upload-input @error('imagen') is-invalid @enderror"
                        id="imagen" name="imagen" accept="image/*" style="display: none;">
                    <div class="image-upload-placeholder {{ $news->imagen ? 'd-none' : '' }}" id="imagePlaceholder">
                        <i class="ri-image-add-line"></i>
                        <span>Haz clic para subir una imagen</span>
                        <small class="text-muted d-block mt-2">Formatos permitidos: JPEG, PNG, JPG, GIF. Tamaño máximo: 20MB</small>
                    </div>
                    <div class="image-preview {{ $news->imagen ? '' : 'd-none' }}" id="imagePreview">
                        <img src="{{ $news->imagen ? asset($news->imagen) : '' }}" alt="Vista previa" id="previewImage">
                        <div class="image-buttons">
                            <button type="button" class="btn btn-danger btn-sm remove-image" id="removeImage">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-sm edit-image" id="editImage">
                                <i class="ri-edit-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @error('imagen')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div id="imageError" class="alert alert-danger d-none mt-2 mb-0" role="alert" style="padding: 0.5rem 1rem; font-size: 1rem;">
                    <span id="imageErrorMsg"></span>
                    <button type="button" class="btn-close float-end" aria-label="Cerrar" onclick="this.parentElement.classList.add('d-none')"></button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-warning mt-3" id="submitBtn">Actualizar Noticia</button>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary mt-3" id="cancelBtn">Cancelar</a>
    </form>
</div>

{{-- Modal de confirmación --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar salida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Hay cambios sin guardar. ¿Desea salir sin guardar los cambios?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, permanecer aquí</button>
                <a href="{{ route('admin.news.index') }}" class="btn btn-primary">Sí, salir sin guardar</a>
            </div>
        </div>
    </div>
</div>

{{-- Modal de recorte de imagen --}}
<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropModalLabel">Recortar Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="cropImage" src="" alt="Imagen para recortar">
                </div>
                <div class="crop-presets mt-3 text-center">
                    <div class="btn-group" role="group" aria-label="Presets de recorte">
                        <button type="button" class="btn btn-outline-primary" data-ratio="16/9">16:9</button>
                        <button type="button" class="btn btn-outline-primary" data-ratio="4/3">4:3</button>
                        <button type="button" class="btn btn-outline-primary" data-ratio="1/1">1:1</button>
                    </div>
                    <button type="button" class="btn btn-outline-secondary ms-2" id="resetCrop">
                        <i class="ri-refresh-line"></i> Restablecer
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="cropButton">Recortar y Aplicar</button>
            </div>
        </div>
    </div>
</div>

{{-- Spinner de carga --}}
<div class="loading-spinner" id="loadingSpinner" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    <p class="mt-2" id="spinnerText">Actualizando noticia...</p>
</div>

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
    .image-upload-container {
        width: 100%;
        max-width: 500px;
    }

    .image-upload-box {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-upload-box:hover {
        border-color: #0d6efd;
        background-color: #f1f3f5;
    }

    .image-upload-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .image-upload-placeholder i {
        font-size: 48px;
        color: #6c757d;
    }

    .image-preview {
        position: relative;
        width: 100%;
        height: 200px;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 4px;
    }

    .image-buttons {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        z-index: 10;
    }

    .image-buttons button {
        padding: 0.25rem 0.5rem;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-buttons button i {
        font-size: 1.1rem;
    }

    .loading-spinner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    /* Estilos para el modal de recorte */
    .img-container {
        max-height: 500px;
        margin-bottom: 1rem;
    }
    
    .img-container img {
        max-width: 100%;
        max-height: 500px;
    }

    .crop-presets {
        margin-top: 1rem;
    }

    .crop-presets .btn-group {
        margin-right: 0.5rem;
    }

    .cropper-container {
        max-height: 500px !important;
    }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageUploadBox = document.getElementById('imageUploadBox');
        const imageInput = document.getElementById('imagen');
        const imagePlaceholder = document.getElementById('imagePlaceholder');
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const removeImage = document.getElementById('removeImage');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const newsForm = document.getElementById('newsForm');
        const submitBtn = document.getElementById('submitBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
        const imageError = document.getElementById('imageError');
        const imageErrorMsg = document.getElementById('imageErrorMsg');
        const cropImage = document.getElementById('cropImage');
        const cropButton = document.getElementById('cropButton');
        const resetCropBtn = document.getElementById('resetCrop');
        let errorTimeout = null;
        let cropper = null;
        let currentFile = null;

        // Variable para controlar si hay cambios sin guardar
        let hasUnsavedChanges = false;

        // Función para mostrar el modal de recorte
        function showCropModal(file) {
            currentFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
                cropImage.src = e.target.result;
                cropModal.show();
                
                // Inicializar Cropper después de que el modal esté visible
                cropModal._element.addEventListener('shown.bs.modal', function onShown() {
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(cropImage, {
                        aspectRatio: 16 / 9,
                        viewMode: 2,
                        responsive: true,
                        restore: false,
                        autoCropArea: 1,
                        movable: false,
                        zoomable: true,
                        rotatable: false,
                        scalable: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                    cropModal._element.removeEventListener('shown.bs.modal', onShown);
                }, { once: true });
            };
            reader.readAsDataURL(file);
        }

        // Botones de proporciones predefinidas
        document.querySelectorAll('.crop-presets button[data-ratio]').forEach(button => {
            button.addEventListener('click', function() {
                if (cropper) {
                    const ratio = this.dataset.ratio.split('/');
                    cropper.setAspectRatio(ratio[0] / ratio[1]);
                }
            });
        });

        // Botón de restablecer
        resetCropBtn.addEventListener('click', function() {
            if (cropper) {
                cropper.reset();
            }
        });

        // Botón de recortar y aplicar
        cropButton.addEventListener('click', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    maxWidth: 1200,
                    maxHeight: 1200,
                    fillColor: '#fff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
                
                canvas.toBlob(function(blob) {
                    // Crear un nuevo archivo con la imagen recortada
                    const croppedFile = new File([blob], currentFile.name, {
                        type: currentFile.type,
                        lastModified: new Date().getTime()
                    });

                    // Actualizar la vista previa
                    previewImage.src = canvas.toDataURL();
                    imagePlaceholder.classList.add('d-none');
                    imagePreview.classList.remove('d-none');

                    // Actualizar el input file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    imageInput.files = dataTransfer.files;

                    // Cerrar el modal
                    cropModal.hide();
                    hasUnsavedChanges = true;
                }, currentFile.type);
            }
        });

        // Click en el contenedor para abrir el selector de archivos
        imageUploadBox.addEventListener('click', () => {
            imageInput.click();
        });

        // Cuando se selecciona una imagen
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño (20MB máximo)
                if (file.size > 20 * 1024 * 1024) {
                    showImageError('El archivo es demasiado grande. El tamaño máximo permitido es 20MB.');
                    imageInput.value = '';
                    return;
                }

                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showImageError('Tipo de archivo no permitido. Solo se permiten imágenes JPEG, PNG, JPG, GIF y WEBP.');
                    imageInput.value = '';
                    return;
                }

                showCropModal(file);
            }
        });

        // Eliminar imagen
        removeImage.addEventListener('click', (e) => {
            e.stopPropagation();
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            imageInput.value = '';
            imagePlaceholder.classList.remove('d-none');
            imagePreview.classList.add('d-none');
            previewImage.src = '';
            hasUnsavedChanges = true;
        });

        // Editar imagen existente
        const editImage = document.getElementById('editImage');
        editImage.addEventListener('click', (e) => {
            e.stopPropagation();
            if (previewImage.src) {
                // Convertir la URL de la imagen a un Blob
                fetch(previewImage.src)
                    .then(res => res.blob())
                    .then(blob => {
                        const file = new File([blob], 'imagen_editada.jpg', { type: blob.type });
                        showCropModal(file);
                    });
            }
        });

        // Drag and drop mejorado
        imageUploadBox.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageUploadBox.style.borderColor = '#0d6efd';
            imageUploadBox.style.backgroundColor = '#f1f3f5';
        });

        imageUploadBox.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageUploadBox.style.borderColor = '#ccc';
            imageUploadBox.style.backgroundColor = '#f8f9fa';
        });

        imageUploadBox.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            imageUploadBox.style.borderColor = '#ccc';
            imageUploadBox.style.backgroundColor = '#f8f9fa';
            
            const file = e.dataTransfer.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    // Validar tamaño (20MB máximo)
                    if (file.size > 20 * 1024 * 1024) {
                        showImageError('El archivo es demasiado grande. El tamaño máximo permitido es 20MB.');
                        return;
                    }

                    // Validar tipo de archivo
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        showImageError('Tipo de archivo no permitido. Solo se permiten imágenes JPEG, PNG, JPG, GIF y WEBP.');
                        return;
                    }

                    imageInput.files = e.dataTransfer.files;
                    showCropModal(file);
                } else {
                    showImageError('Tipo de archivo no permitido. Solo se permiten imágenes.');
                }
            }
        });

        // Mostrar spinner durante la subida
        newsForm.addEventListener('submit', (e) => {
            loadingSpinner.style.display = 'flex';
            document.getElementById('spinnerText').textContent = 'Actualizando noticia...';
            submitBtn.disabled = true;
        });

        // Manejar el botón de cancelar
        cancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (hasUnsavedChanges) {
                confirmModal.show();
            } else {
                window.location.href = cancelBtn.href;
            }
        });

        // Código para el botón de fecha actual
        const fechaActualBtn = document.getElementById('fechaActual');
        const fechaInput = document.getElementById('fecha_publicacion');

        fechaActualBtn.addEventListener('click', function() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const fechaHora = `${year}-${month}-${day}T${hours}:${minutes}`;
            fechaInput.value = fechaHora;
            hasUnsavedChanges = true;
        });

        // Función para mostrar errores de imagen
        function showImageError(msg) {
            imageErrorMsg.textContent = msg;
            imageError.classList.remove('d-none');
            if (errorTimeout) clearTimeout(errorTimeout);
            errorTimeout = setTimeout(() => {
                imageError.classList.add('d-none');
            }, 6000);
        }

        // Botón de editar imagen
        document.getElementById('editImageBtn').addEventListener('click', function() {
            // Si hay una imagen existente, la guardamos temporalmente
            const existingImage = document.getElementById('imagePreview').querySelector('img');
            if (existingImage) {
                // Guardamos la URL de la imagen original
                const originalImageUrl = existingImage.src;
                // Limpiamos el input file para permitir seleccionar la misma imagen
                document.getElementById('imagen').value = '';
                // Abrimos el selector de archivos
                document.getElementById('imagen').click();
            }
        });

        // Modificar el evento change del input file
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Guardamos la imagen original en el cropper
                    cropper.replace(e.target.result);
                    // Mostramos el modal
                    $('#cropModal').modal('show');
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
@endsection