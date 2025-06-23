@extends('template.base')

@section('title', 'Crear Curso')

@section('content')
    <h1>Crear Curso</h1>

    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="ri-close-circle-fill text-danger me-2 fs-4"></i>
            <div>
                <strong>Errores encontrados:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.cursos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="titulo" class="form-label">
                        <i class="ri-book-line me-1"></i>Título
                    </label>
                    <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}" required>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-file-text-line me-1"></i>Descripción
                    </label>
                    <textarea name="descripcion" id="descripcion" class="form-control" rows="4">{{ old('descripcion') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label">
                                <i class="ri-calendar-line me-1"></i>Fecha de Inicio
                            </label>
                            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control" value="{{ old('fechaInicio') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fechaFin" class="form-label">
                                <i class="ri-calendar-check-line me-1"></i>Fecha de Fin
                            </label>
                            <input type="date" name="fechaFin" id="fechaFin" class="form-control" value="{{ old('fechaFin') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="plazas" class="form-label">
                                <i class="ri-seat-line me-1"></i>Plazas
                            </label>
                            <input type="number" name="plazas" id="plazas" class="form-control" value="{{ old('plazas') }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="precio" class="form-label">
                                <i class="ri-money-euro-circle-line me-1"></i>Precio
                            </label>
                            <input type="number" step="0.01" name="precio" id="precio" class="form-control" value="{{ old('precio') }}" min="0">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="estado" class="form-label">
                        <i class="ri-toggle-line me-1"></i>Estado
                    </label>
                    <select name="estado" id="estado" class="form-control" required>
                        <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ri-image-line me-2"></i>
                            Imagen de Portada
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="file" name="portada" id="portada" class="form-control" accept="image/*">
                            <small class="text-muted">
                                Formatos: JPG, PNG, WEBP (máx. 2MB)
                            </small>
                        </div>
                        <div id="preview-container" style="display: none;">
                            <img id="image-preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
                <i class="ri-save-line me-2"></i>Guardar Curso
            </button>
            <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-2"></i>Cancelar
            </a>
        </div>
    </form>
@endsection

@push('js')
<script>
    // Preview de imagen - Muestra vista previa al seleccionar archivo
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
</script>
@endpush