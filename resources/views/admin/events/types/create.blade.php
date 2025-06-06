@extends('template.base')

@section('content')
<div class="container">
    <h1>Crear nuevo tipo de evento</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tipos-evento.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
        </div>

        <div class="mb-3">
            <label for="color" class="form-label">Color (hexadecimal)</label>
            <input type="color" name="color" id="color" class="form-control form-control-color" value="{{ old('color', '#3788d8') }}">
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Activo</label>
            <select name="status" id="status" class="form-control">
                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar tipo de evento</button>
        <a href="{{ route('tipos-evento.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
