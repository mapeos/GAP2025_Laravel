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
                        <small class="text-muted d-block mt-2">Formatos permitidos: JPEG, PNG, JPG, GIF. Tamaño máximo: 10MB</small>
                    </div>
                    <div class="image-preview {{ $news->imagen ? '' : 'd-none' }}" id="imagePreview">
                        <img src="{{ $news->imagen ? asset($news->imagen) : '' }}" alt="Vista previa" id="previewImage">
                        <button type="button" class="btn btn-danger btn-sm remove-image" id="removeImage">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
                @error('imagen')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
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

{{-- Spinner de carga --}}
<div class="loading-spinner" id="loadingSpinner" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
    <p class="mt-2" id="spinnerText">Actualizando noticia...</p>
</div>

@push('css')
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

    .remove-image {
        position: absolute;
        top: 10px;
        right: 10px;
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
</style>
@endpush

@push('js')
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

        // Variable para controlar si hay cambios sin guardar
        let hasUnsavedChanges = false;

        // Función para mostrar la vista previa
        function showPreview(file) {
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePlaceholder.classList.add('d-none');
                    imagePreview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        }

        // Click en el contenedor para abrir el selector de archivos
        imageUploadBox.addEventListener('click', () => {
            imageInput.click();
        });

        // Cuando se selecciona una imagen
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                showPreview(file);
                hasUnsavedChanges = true;
            }
        });

        // Eliminar imagen
        removeImage.addEventListener('click', (e) => {
            e.stopPropagation();
            imageInput.value = '';
            imagePlaceholder.classList.remove('d-none');
            imagePreview.classList.add('d-none');
            previewImage.src = '';
            hasUnsavedChanges = true;
        });

        // Detectar cambios en el formulario
        const formInputs = newsForm.querySelectorAll('input, textarea, select');
        formInputs.forEach(input => {
            input.addEventListener('change', () => {
                hasUnsavedChanges = true;
            });
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

        // Drag and drop
        imageUploadBox.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadBox.style.borderColor = '#0d6efd';
        });

        imageUploadBox.addEventListener('dragleave', () => {
            imageUploadBox.style.borderColor = '#ccc';
        });

        imageUploadBox.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadBox.style.borderColor = '#ccc';
            
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                imageInput.files = e.dataTransfer.files;
                showPreview(file);
                hasUnsavedChanges = true;
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
    });
</script>
@endpush
@endsection