@extends('template.base-admin')

@section('title', 'Cursos Activos')

@section('content')
    <h1>Cursos Activos</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->titulo }}</td>
                    <td>{{ $curso->fechaInicio }}</td>
                    <td>{{ $curso->fechaFin }}</td>
                    <td>
                        <a href="{{ route('admin.inscripciones.cursos.inscribir', $curso->id) }}" class="btn btn-sm btn-primary">Inscribir</a>
                        <a href="{{ route('admin.inscripciones.cursos.inscritos', $curso->id) }}" class="btn btn-sm btn-info">Ver Inscritos</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay cursos activos disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection