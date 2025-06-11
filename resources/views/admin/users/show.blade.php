@extends('template.base')

@section('title', 'Perfil de Usuario')
@section('title-sidebar', 'Usuarios')
@section('title-page', 'Perfil de Usuario')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Perfil de {{ $user->name }}</h5>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Volver</a>
    </div>
    <div class="card-body row">
        <div class="col-md-4 text-center">
            <i class="ri-user-3-fill display-1 text-primary"></i>
            <h4 class="mt-3">{{ $user->name }}</h4>
            <p class="text-muted">{{ $user->email }}</p>
            <span class="badge bg-info">{{ $user->status }}</span>
        </div>
        <div class="col-md-8">
            <table class="table table-borderless">
                <tr>
                    <th>ID:</th>
                    <td>{{ $user->id }}</td>
                </tr>
                <tr>
                    <th>Nombre:</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Roles:</th>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-secondary">{{ $role->name }}</span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td><span class="badge bg-{{ $user->status === 'activo' ? 'success' : 'warning' }}">{{ ucfirst($user->status) }}</span></td>
                </tr>
                <tr>
                    <th>Creado:</th>
                    <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Actualizado:</th>
                    <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
