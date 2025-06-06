@extends('template.base')

@section('content')
<div class="container">
    <h1>Editar evento</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('eventos.update', $evento) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo', $evento->titulo) }}" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion', $evento->descripcion) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
            <input type="datetime-local" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', \Carbon\Carbon::parse($evento->fecha_inicio)->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha de fin</label>
            <input type="datetime-local" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ old('fecha_fin', \Carbon\Carbon::parse($evento->fecha_fin)->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="ubicacion" class="form-label">Ubicación</label>
            <input type="text" name="ubicacion" id="ubicacion" class="form-control" value="{{ old('ubicacion', $evento->ubicacion) }}">
        </div>

        <div class="mb-3">
            <label for="url_virtual" class="form-label">URL Virtual</label>
            <input type="url" name="url_virtual" id="url_virtual" class="form-control" value="{{ old('url_virtual', $evento->url_virtual) }}">
        </div>

        <div class="mb-3">
            <label for="tipo_evento_id" class="form-label">Tipo de evento</label>
            <select name="tipo_evento_id" id="tipo_evento_id" class="form-control" required>
                <option value="">Seleccione...</option>
                @foreach($tiposEvento as $tipo)
                    <option value="{{ $tipo->id }}" {{ old('tipo_evento_id', $evento->tipo_evento_id) == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Activo</label>
            <select name="status" id="status" class="form-control">
                <option value="1" {{ old('status', $evento->status) == 1 ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('status', $evento->status) == 0 ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar evento</button>
        <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
