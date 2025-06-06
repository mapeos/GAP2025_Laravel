@extends('template.base')

@section('title', 'Detalles de Participante')

@section('content')
    <h1>Detalles de {{ $persona->nombre }} {{ $persona->apellido1 }} {{ $persona->apellido2 }}</h1>
    <div class="card">
        <div class="card-body">
            <img src="{{ $persona->imagen }}" alt="Imagen de {{ $persona->nombre }}" class="img-thumbnail mb-3" style="max-width: 200px;">
            <p><strong>DNI:</strong> {{ $persona->dni }}</p>
            <p><strong>Teléfono:</strong> {{ $persona->tfno }}</p>
            <p><strong>Dirección:</strong> {{ $persona->direccion->calle ?? 'Sin dirección' }}</p>
            <p><strong>Ciudad:</strong> {{ $persona->direccion->ciudad ?? 'Sin ciudad' }}</p>
            <p><strong>Provincia:</strong> {{ $persona->direccion->provincia ?? 'Sin provincia' }}</p>
            <p><strong>País:</strong> {{ $persona->direccion->pais ?? 'Sin país' }}</p>
        </div>
    </div>
@endsection