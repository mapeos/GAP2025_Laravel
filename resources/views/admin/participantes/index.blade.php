@extends('template.base')

@section('title', 'Listar Participantes')

@section('content')
    <h1>Lista de Participantes</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Usuario Asociado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($personas as $persona)
                <tr>
                    <td>{{ $persona->id }}</td>
                    <td>{{ $persona->nombre }} {{ $persona->apellido1 }} {{ $persona->apellido2 }}</td>
                    <td>{{ $persona->user->email ?? 'Sin Usuario Asociado' }}</td>
                    <td>
                        <a href="{{ route('admin.participantes.show', $persona->id) }}" class="btn btn-sm btn-info">Ver Detalles</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay participantes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection