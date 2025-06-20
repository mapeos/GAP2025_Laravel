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