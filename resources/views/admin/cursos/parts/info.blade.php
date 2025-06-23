<div class="card mb-4 w-100 position-relative">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div class="flex-grow-1 text-center">
            <span class="fw-bold fs-3">Información del Curso</span>
        </div>
        <a href="{{ route('admin.cursos.edit', $curso->id) }}"
           class="btn btn-warning btn-lg fw-bold shadow ms-3"
           style="font-size: 1.1rem; padding: 0.5rem 1.5rem;"
           title="Editar curso">
            <i class="fa fa-edit me-2"></i> Editar
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 d-flex align-items-center justify-content-center">
                @if ($curso->portada_path)
                    <img src="{{ asset('storage/' . $curso->portada_path) }}" alt="Portada del curso" class="img-fluid rounded" style="max-width: 100%; max-height: 250px;">
                @else
                    <span class="text-muted">Sin portada</span>
                @endif
            </div>
            <div class="col-md-9">
                <h2>{{ $curso->titulo }}</h2>
                <p>{{ $curso->descripcion }}</p>
                <p><strong>Fechas:</strong> {{ $curso->fechaInicio }} - {{ $curso->fechaFin }}</p>
                <p><strong>Plazas:</strong> {{ $curso->plazas }}</p>
                <p><strong>Estado:</strong> {{ $curso->estado }}</p>
                <p><strong>Ubicación:</strong> {{ $curso->ubicacion ?? 'No especificada' }}</p>
                <p><strong>Precio:</strong> {{ $curso->precio ?? 'No especificado' }}</p>
                <p><strong>Profesor:</strong> {{ $curso->profesor ?? 'No asignado' }}</p>
            </div>
        </div>
    </div>
</div>