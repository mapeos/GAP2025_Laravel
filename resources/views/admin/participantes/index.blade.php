@extends('template.base-admin')

@section('title', 'Lista de Participantes')

@section('content')
    <h1>Lista de Participantes</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($personas as $persona)
                <tr>
                    <td>{{ $persona->id }}</td>
                    <td>{{ $persona->nombre }} {{ $persona->apellido1 }} {{ $persona->apellido2 }}</td>
                    <td>{{ $persona->tfno }}</td>
                    <td>
                        <a href="{{ route('admin.participantes.show', $persona->id) }}" class="btn btn-sm btn-info">Ver Detalles</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay participantes disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection