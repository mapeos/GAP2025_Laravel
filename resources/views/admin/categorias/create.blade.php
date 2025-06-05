@extends('template.base')

@section('title', 'Crear Categoría')
@section('title-sidebar', 'Categorías')
@section('title-page', 'Crear Categoría')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.categorias.index') }}">Categorías</a></li>
<li class="breadcrumb-item active">Crear Categoría</li>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Nueva Categoría</h1>
    
    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    @include('template.partials.alerts')

    <form action="{{ route('admin.categorias.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="45">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control" maxlength="255">{{ old('descripcion') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection