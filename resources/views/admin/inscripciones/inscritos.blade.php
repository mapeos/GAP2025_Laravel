@extends('template.base')

@section('title', 'Personas Inscritas')

@section('content')
    <h1>Personas Inscritas en {{ $curso->titulo }}</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inscritos as $inscrito)
                <tr>
                    <td>{{ $inscrito->id }}</td>
                    <td>{{ $inscrito->nombre }} {{ $inscrito->apellido1 }} {{ $inscrito->apellido2 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">No hay personas inscritas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection