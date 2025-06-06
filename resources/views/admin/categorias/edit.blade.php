@extends('template.base-admin')

@section('title', 'Editar Categoría')
@section('title-sidebar', 'Categorías')
@section('title-page', 'Editar Categoría')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.categorias.index') }}">Categorías</a></li>
<li class="breadcrumb-item active">Editar Categoría</li>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Categoría</h1>
    
    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    @include('template.partials.alerts')

    <form action="{{ route('admin.categorias.update', $categoria) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $categoria->nombre) }}" required maxlength="45">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control" maxlength="255">{{ old('descripcion', $categoria->descripcion) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection