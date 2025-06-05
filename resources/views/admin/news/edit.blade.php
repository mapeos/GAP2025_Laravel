@extends('template.base-admin')

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

    <form action="{{ route('admin.news.update', $news) }}" method="POST">
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
            <input type="datetime-local" class="form-control @error('fecha_publicacion') is-invalid @enderror" id="fecha_publicacion" name="fecha_publicacion"
                value="{{ old('fecha_publicacion', optional($news->fecha_publicacion)->format('Y-m-d\TH:i')) }}" required>
            @error('fecha_publicacion')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
@endsection