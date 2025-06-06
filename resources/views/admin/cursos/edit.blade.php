@extends('template.base')

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
    </form>
@endsection