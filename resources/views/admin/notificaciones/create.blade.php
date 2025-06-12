@extends('template.base')

@section('title', 'Crear Notificación')
@section('title-sidebar', 'Notificaciones')
@section('title-page', 'Crear Notificación')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.notificaciones.index') }}">Notificaciones</a></li>
<li class="breadcrumb-item active">Crear Notificación</li>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Enviar Nueva Notificación</h1>

    {{-- Flash messages (success, error, validation) --}}
    @include('template.partials.alerts')

    <form action="{{ route('admin.notificaciones.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Título *</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required maxlength="100">
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Mensaje *</label>
            <textarea id="body" name="body" class="form-control" rows="3" required maxlength="255">{{ old('body') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="users" class="form-label">Seleccionar Usuarios *</label>
            <select name="users[]" id="users" class="form-select" multiple required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            <small class="form-text text-muted">Mantén presionado Ctrl (Windows) o Cmd (Mac) para seleccionar varios usuarios.</small>
        </div>

        <button type="submit" class="btn btn-primary">Enviar Notificación</button>
        <a href="{{ route('admin.notificaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
