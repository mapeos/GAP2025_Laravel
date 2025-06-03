@extends('template.base')

@section('title', 'Cursos')

@section('content')
    <h1>Cursos</h1>

    <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary mb-3">Crear curso</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Plazas</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->titulo }}</td>
                    <td>{{ $curso->descripcion }}</td>
                    <td>{{ $curso->fechaInicio }}</td>
                    <td>{{ $curso->fechaFin }}</td>
                    <td>{{ $curso->plazas }}</td>
                    <td>{{ $curso->estado }}</td>
                    <td>
                        <a href="{{ route('admin.cursos.edit', $curso->id) }}" class="btn btn-sm btn-warning">Modificar</a>
                        <form action="{{ route('admin.cursos.destroy', $curso->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este curso?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No hay cursos disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection