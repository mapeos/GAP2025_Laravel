@extends('template.base')

@section('title', 'Crear Curso')

@section('content')
<h1>Crear Curso</h1>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('cursos.store') }}" method="POST" enctype="multipart/form-data">    @csrf
    <div class="mb-3">
        <label for="portada" class="form-label">Imagen de Portada</label>
        <input type="file" name="portada" id="portada" class="form-control" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="titulo" class="form-label">Título</label>
        <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}" required>
    </div>
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
        <input type="date" name="fechaInicio" id="fechaInicio" class="form-control" value="{{ old('fechaInicio') }}" required>
    </div>
    <div class="mb-3">
        <label for="fechaFin" class="form-label">Fecha de Fin</label>
        <input type="date" name="fechaFin" id="fechaFin" class="form-control" value="{{ old('fechaFin') }}" required>
    </div>
    <div class="mb-3">
        <label for="plazas" class="form-label">Plazas</label>
        <input type="number" name="plazas" id="plazas" class="form-control" value="{{ old('plazas') }}" required>
    </div>
    <div class="mb-3">
        <label for="estado" class="form-label">Estado</label>
        <select name="estado" id="estado" class="form-control" required>
            <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection