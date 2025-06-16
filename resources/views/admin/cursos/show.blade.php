@extends('template.base')

@section('title', 'Detalles del Curso')

@section('content')
    <h1>Detalles del Curso: {{ $curso->titulo }}</h1>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $curso->id }}</td>
        </tr>
        <tr>
            <th>Título</th>
            <td>{{ $curso->titulo }}</td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td>{{ $curso->descripcion }}</td>
        </tr>
        <tr>
            <th>Fecha Inicio</th>
            <td>{{ $curso->fechaInicio }}</td>
        </tr>
        <tr>
            <th>Fecha Fin</th>
            <td>{{ $curso->fechaFin }}</td>
        </tr>
        <tr>
            <th>Plazas</th>
            <td>{{ $curso->plazas }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $curso->estado }}</td>
        </tr>
    </table>

    <h2>Subir Temario</h2>
    <form action="{{ route('admin.cursos.upload', $curso->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="temario">Seleccionar archivo:</label>
            <input type="file" name="temario" id="temario" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Subir Temario</button>
    </form>

    <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
@endsection