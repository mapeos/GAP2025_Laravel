<div class="card h-100">
    <div class="card-header bg-info text-white">
        Temario
    </div>
    {{-- Separador visual --}}
    <div style="height: 24px;"></div>
    <div class="card-body">
        @if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
            <a href="{{ asset('storage/' . $curso->temario_path) }}" target="_blank" class="btn btn-info">
                📄 Ver/Descargar Temario
            </a>
        @else
            <p>No se ha subido ningún temario.</p>
        @endif

        <form action="{{ route('admin.cursos.upload', $curso->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
            @csrf
            <div>
                <label for="temario"></label>
                <input type="file" name="temario" accept=".pdf,.doc,.docx" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Subir temario</button>
        </form>
    </div>
</div>