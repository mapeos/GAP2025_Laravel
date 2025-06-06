@extends('template.base')

@section('title', 'Crear Noticia')
@section('title-sidebar', 'Noticias')
@section('title-page', 'Crear Noticia')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">News</a></li>
<li class="breadcrumb-item active">Create News</li>
@endsection

@section('content')
<div class="container">
    <h1>Crear Noticia</h1>

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    @include('template.partials.alerts')

    <form action="{{ route('admin.news.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="titulo">Título</label>
            <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" value="{{ old('titulo') }}" required>
            @error('titulo')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="contenido">Contenido</label>
            <textarea class="form-control @error('contenido') is-invalid @enderror" id="contenido" name="contenido" rows="5" required>{{ old('contenido') }}</textarea>
            @error('contenido')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="autor">Autor</label>
            <input type="number" class="form-control @error('autor') is-invalid @enderror" id="autor" name="autor" value="{{ old('autor') }}">
            @error('autor')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="fecha_publicacion">Fecha de Publicación</label>
            <input type="datetime-local" class="form-control @error('fecha_publicacion') is-invalid @enderror" id="fecha_publicacion" name="fecha_publicacion" value="{{ old('fecha_publicacion') }}" required>
            @error('fecha_publicacion')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- <div class="form-group mb-3">
            <label for="categorias">Categorías</label>
            <div>
                @foreach($categorias as $categoria)
                <div class="form-check">
                    <input id="categoria_{{ $categoria->id }}" class="form-check-input" type="checkbox" name="categorias[]" value="{{ $categoria->id }}"
                        @if(in_array($categoria->id, old('categorias', []))) checked @endif>
                    <label class="form-check-label" for="categoria_{{ $categoria->id }}">
                        {{ $categoria->nombre }}
                    </label>
                </div>
                @endforeach
            </div>
        </div> -->

        <div class="form-group mb-3">
            <label for="categorias">Categorías</label>
            <select class="form-control select2-categorias" name="categorias[]" id="categorias">
                <option value="" disabled selected>Seleccionar Categorías...</option>
                @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-3">Crear Noticia</button>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary mt-3">Cancelar</a>

    </form>
</div>
@endsection