@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalle del evento</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title">{{ $evento->titulo }}</h3>
            <p class="card-text"><strong>Descripción:</strong> {{ $evento->descripcion ?? 'Sin descripción' }}</p>
            <p class="card-text"><strong>Fecha de inicio:</strong> {{ $evento->fecha_inicio }}</p>
            <p class="card-text"><strong>Fecha de fin:</strong> {{ $evento->fecha_fin }}</p>
            <p class="card-text"><strong>Ubicación:</strong> {{ $evento->ubicacion ?? 'No especificada' }}</p>
            <p class="card-text"><strong>URL Virtual:</strong>
                @if($evento->url_virtual)
                    <a href="{{ $evento->url_virtual }}" target="_blank">{{ $evento->url_virtual }}</a>
                @else
                    No especificada
                @endif
            </p>
            <p class="card-text"><strong>Tipo de evento:</strong> {{ $evento->tipoEvento->nombre ?? '-' }}</p>
            <p class="card-text"><strong>Estado:</strong> {{ $evento->status ? 'Activo' : 'Inactivo' }}</p>
        </div>
    </div>

    <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-warning">Editar</a>
    <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Volver al listado</a>
</div>
@endsection
