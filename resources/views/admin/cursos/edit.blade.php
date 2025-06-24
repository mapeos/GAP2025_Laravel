@extends('template.base')

@section('title', 'Editar Curso')

@section('content')
    <h1>Editar Curso</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.cursos.update', $curso->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ $curso->titulo }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4">{{ $curso->descripcion }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
        <div class="mb-3">
            <label for="fechaInicio" class="form-label">Fecha Inicio</label>
            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control" value="{{ $curso->fechaInicio }}" required>
        </div>
            </div>
            <div class="col-md-6">
        <div class="mb-3">
            <label for="fechaFin" class="form-label">Fecha Fin</label>
            <input type="date" name="fechaFin" id="fechaFin" class="form-control" value="{{ $curso->fechaFin }}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
        <div class="mb-3">
            <label for="plazas" class="form-label">Plazas</label>
                    <input type="number" name="plazas" id="plazas" class="form-control" value="{{ $curso->plazas }}" min="1" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" name="precio" id="precio" class="form-control" value="{{ $curso->precio }}" min="0" step="0.01">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="activo" {{ $curso->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ $curso->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="portada" class="form-label">Imagen de Portada</label>
            <input type="file" name="portada" id="portada" class="form-control" accept="image/*">
            @if ($curso->portada_path)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $curso->portada_path) }}" alt="Portada actual" style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                    <small class="text-muted">Imagen actual</small>
                </div>
            @endif
        </div>

        <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>

    {{-- Sección para subir temario --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Gestión de Temario</h5>
        </div>
        <div class="card-body">
            @if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
                <div class="mb-3">
                    <a href="{{ asset('storage/' . $curso->temario_path) }}" target="_blank" class="btn btn-info">
                        <i class="ri-file-text-line me-2"></i> Ver/Descargar Temario Actual
                    </a>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i>
                    No se ha subido ningún temario.
                </div>
            @endif

            <form action="{{ route('admin.cursos.upload', $curso->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="temario" class="form-label">Subir nuevo temario</label>
                    <input type="file" name="temario" id="temario" class="form-control" accept=".pdf,.doc,.docx" required>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="ri-upload-line me-2"></i> Subir temario
                </button>
            </form>
        </div>
    </div>
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

    // Validación de fechas - Asegura que fecha fin sea posterior a fecha inicio
    document.getElementById('fechaInicio').addEventListener('change', function() {
        const fechaInicio = this.value;
        const fechaFin = document.getElementById('fechaFin').value;
        
        if (fechaFin && fechaInicio > fechaFin) {
            alert('La fecha de fin debe ser posterior a la fecha de inicio');
            this.value = '';
        }
    });

    document.getElementById('fechaFin').addEventListener('change', function() {
        const fechaFin = this.value;
        const fechaInicio = document.getElementById('fechaInicio').value;
        
        if (fechaInicio && fechaFin < fechaInicio) {
            alert('La fecha de fin debe ser posterior a la fecha de inicio');
            this.value = '';
        }
    });
</script>
@endpush
