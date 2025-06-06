@extends('template.base')
@section('title', 'Usuarios pendientes de validar')
@section('title-sidebar', 'Usuarios pendientes')
@section('title-page', 'Usuarios pendientes de validar')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Usuarios pendientes de validación</h4>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Volver</a>
        </div>
        <div class="card-body">
            @if($users->isEmpty())
                <div class="alert alert-info">No hay usuarios pendientes de validar.</div>
            @else
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Fecha de registro</th>
                            <th>Asignar Rol</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.validate.bulk') }}" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm" required>
                                        <option value="">Selecciona un rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td>
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-success btn-sm">Validar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
