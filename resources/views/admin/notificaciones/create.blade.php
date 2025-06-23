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

        {{-- Filtros avanzados --}}
        <div class="mb-3 row">
            <div class="col-md-4">
                <label for="course_filter" class="form-label">Filtrar por curso</label>
                <select id="course_filter" class="form-select">
                    <option value="">Todos los cursos</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->titulo }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="role_filter" class="form-label">Filtrar por rol</label>
                <select id="role_filter" class="form-select">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="estado_filter" class="form-label">Estado</label>
                <select id="estado_filter" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Seleccionar Usuarios *</label>
            <div id="users-checkbox-list">
                @foreach ($users as $user)
                    <div class="form-check user-checkbox"
                        data-course="@foreach($user->persona && $user->persona->cursos ? $user->persona->cursos : [] as $curso){{ $curso->id }} @endforeach"
                        data-role="{{ $user->getRoleNames()->implode(',') }}"
                        data-estado="{{ in_array($user->id, $activos) ? 'activo' : 'inactivo' }}">
                        <input class="form-check-input" type="checkbox" name="users[]" value="{{ $user->id }}" id="user_{{ $user->id }}">
                        <label class="form-check-label" for="user_{{ $user->id }}">
                            {{ $user->name }} ({{ $user->email }})
                            @if($user->persona && $user->persona->cursos)
                                -
                                @foreach($user->persona->cursos as $curso)
                                    {{ $curso->titulo }}@if(!$loop->last), @endif
                                @endforeach
                            @endif
                            @if($user->getRoleNames()->count())
                                [{{ $user->getRoleNames()->implode(', ') }}]
                            @endif
                            @if(in_array($user->id, $activos))
                                <span class="badge bg-success">Activo</span>
                            @elseif(in_array($user->id, $inactivos))
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
            <small class="form-text text-muted">Marca los usuarios a los que deseas enviar la notificación.</small>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Notificación</button>
        <a href="{{ route('admin.notificaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
{{-- Script para filtrar usuarios por curso, rol y estado --}}
<script>
    function filtrarUsuarios() {
        var curso = document.getElementById('course_filter').value;
        var rol = document.getElementById('role_filter').value.toLowerCase();
        var estado = document.getElementById('estado_filter').value;
        document.querySelectorAll('.user-checkbox').forEach(function(div) {
            var mostrar = true;
            if (curso && !(div.getAttribute('data-course') || '').split(' ').includes(curso)) mostrar = false;
            if (rol) {
                var rolesDiv = (div.getAttribute('data-role') || '').toLowerCase().replace(/\s/g, '');
                if (!rolesDiv.split(',').includes(rol)) mostrar = false;
            }
            if (estado && div.getAttribute('data-estado') !== estado) mostrar = false;
            div.style.display = mostrar ? '' : 'none';
        });
    }
    document.getElementById('course_filter').addEventListener('change', filtrarUsuarios);
    document.getElementById('role_filter').addEventListener('change', filtrarUsuarios);
    document.getElementById('estado_filter').addEventListener('change', filtrarUsuarios);
</script>
@endsection
