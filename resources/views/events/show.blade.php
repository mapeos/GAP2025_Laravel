@extends('template.base-admin')

@section('content')
<div class="container">
    <h1>Detalle del evento</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title">{{ $evento->titulo }}</h3>
            <p><strong>Descripción:</strong> {{ $evento->descripcion ?? 'Sin descripción' }}</p>
            <p><strong>Fecha de inicio:</strong> {{ $evento->fecha_inicio }}</p>
            <p><strong>Fecha de fin:</strong> {{ $evento->fecha_fin }}</p>
            <p><strong>Ubicación:</strong> {{ $evento->ubicacion ?? 'No especificada' }}</p>
            <p><strong>URL Virtual:</strong>
                @if($evento->url_virtual)
                    <a href="{{ $evento->url_virtual }}" target="_blank">{{ $evento->url_virtual }}</a>
                @else
                    No especificada
                @endif
            </p>
            <p><strong>Tipo de evento:</strong> {{ $evento->tipoEvento->nombre ?? '-' }}</p>
            <p><strong>Estado:</strong> {{ $evento->status ? 'Activo' : 'Inactivo' }}</p>
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
