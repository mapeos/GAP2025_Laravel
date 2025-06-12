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

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
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
            <label for="imagen">Imagen</label>
            @if($news->imagen)
                <div class="mb-2">
                    <img src="{{ asset($news->imagen) }}" alt="Imagen actual" class="img-thumbnail" style="max-width: 200px;">
                </div>
            @endif
            <input type="file" class="form-control @error('imagen') is-invalid @enderror" id="imagen" name="imagen" accept="image/*">
            @error('imagen')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Formatos permitidos: JPEG, PNG, JPG, GIF. Tamaño máximo: 2MB</small>
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

        <button type="submit" class="btn btn-warning mt-3">Actualizar Noticia</button>
    </form>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        });
    });
</script>
@endpush
@endsection