@extends('template.base')

@section('title', 'Personas Inscritas')

@section('content')
    <h1>Personas Inscritas en {{ $curso->titulo }}</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inscritos as $inscrito)
                <tr>
                    <td>{{ $inscrito->id }}</td>
                    <td>{{ $inscrito->nombre }} {{ $inscrito->apellido1 }} {{ $inscrito->apellido2 }}</td>
                    <td>{{ $roles[$inscrito->pivot->rol_participacion_id]->nombre ?? 'Sin Rol' }}</td>
                    <td>
                        <form action="{{ route('admin.inscripciones.cursos.baja', [$curso->id, $inscrito->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Dar de Baja</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay personas inscritas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection