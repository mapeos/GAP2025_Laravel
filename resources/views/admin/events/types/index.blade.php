@extends('template.base')

@section('content')
<div class="container">
    <h1 class="mb-4">Tipos de evento</h1>

    <a href="{{ route('tipos-evento.create') }}" class="btn btn-primary mb-3">Crear nuevo tipo de evento</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($tiposEvento->isEmpty())
        <p>No hay tipos de evento registrados.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Color</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tiposEvento as $tipo)
                    <tr>
                        <td>{{ $tipo->nombre }}</td>
                        <td>
                            <span style="background: {{ $tipo->color }}; padding: 3px 10px; border-radius: 4px; color: #fff;">
                                {{ $tipo->color }}
                            </span>
                        </td>
                        <td>{{ $tipo->status ? 'Activo' : 'Inactivo' }}</td>
                        <td>
                            <a href="{{ route('tipos-evento.show', $tipo) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('tipos-evento.edit', $tipo) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('tipos-evento.destroy', $tipo) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este tipo de evento?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
