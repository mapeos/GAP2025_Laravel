<div class="card h-100 shadow-lg border-0 rounded-4">
    <div class="card-header bg-gradient bg-info text-white d-flex align-items-center" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
        <i class="fa fa-book-open me-2"></i>
        <span class="fs-5 fw-semibold">Temario</span>
    </div>
    <div class="card-body bg-light" style="border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
        @if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
            <div class="mb-4 text-center">
                <a href="{{ asset('storage/' . $curso->temario_path) }}" target="_blank" class="btn btn-outline-info btn-lg px-4 py-2 shadow-sm">
                    <i class="fa fa-file-pdf-o me-2"></i> Ver/Descargar Temario
                </a>
            </div>
        @else
            <div class="alert alert-warning text-center mb-4">
                <i class="fa fa-exclamation-circle me-2"></i>
                No se ha subido ningún temario.
            </div>
        @endif

<<<<<<< HEAD
@section('title', 'Editar Curso')

@section('content')
    <h1>Editar Curso</h1>

    <form action="{{ route('admin.cursos.update', $curso->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ $curso->titulo }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" required>{{ $curso->descripcion }}</textarea>
        </div>

        <div class="mb-3">
            <label for="fechaInicio" class="form-label">Fecha Inicio</label>
            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control" value="{{ $curso->fechaInicio }}" required>
        </div>

        <div class="mb-3">
            <label for="fechaFin" class="form-label">Fecha Fin</label>
            <input type="date" name="fechaFin" id="fechaFin" class="form-control" value="{{ $curso->fechaFin }}" required>
        </div>

        <div class="mb-3">
            <label for="plazas" class="form-label">Plazas</label>
            <input type="number" name="plazas" id="plazas" class="form-control" value="{{ $curso->plazas }}" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="activo" {{ $curso->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ $curso->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection
=======
        <form action="{{ route('admin.cursos.upload', $curso->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
            @csrf
            <div class="mb-3">
                <label for="temario" class="form-label fw-semibold">Subir nuevo temario</label>
                <input type="file" name="temario" id="temario" class="form-control" accept=".pdf,.doc,.docx" required>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg shadow">
                    <i class="fa fa-upload me-2"></i> Subir temario
                </button>
            </div>
        </form>
    </div>
</div>
>>>>>>> 5c3efe0af35118b67f3be72ca4f270b9063c0b3a
