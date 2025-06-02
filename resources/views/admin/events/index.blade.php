@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Eventos</h1>

    <a href="{{ route('eventos.create') }}" class="btn btn-primary mb-3">Crear nuevo evento</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($eventos->isEmpty())
        <p>No hay eventos registrados.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($eventos as $evento)
                    <tr>
                        <td>{{ $evento->titulo }}</td>
                        <td>{{ $evento->fecha_inicio }}</td>
                        <td>{{ $evento->fecha_fin }}</td>
                        <td>{{ $evento->tipoEvento->nombre ?? '-' }}</td>
                        <td>
                            <a href="{{ route('eventos.show', $evento) }}" class="btn btn-info btn-sm">Ver</a>
                            <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-warning btn-sm">Editar</a>
                            <form action="{{ route('eventos.destroy', $evento) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este evento?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
