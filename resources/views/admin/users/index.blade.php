@extends('template.base')

@section('title', 'Listado de Usuarios')
@section('title-sidebar', 'Usuarios')
@section('title-page', 'Listado de Usuarios')
@section('breadcrumb')
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Usuarios</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            Añadir Usuario
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Creado por</th>
                    <th>Modificado por</th>
                    <th>Eliminado por</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Muestra la lista de usuarios con información y estado (activo/eliminado).  
                    Permite editar o eliminar usuarios activos y restaurar usuarios eliminados. -->
                @forelse ($users as $user)
                    <tr @if($user->trashed()) class="table-secondary" @endif>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->getRoleNames()->implode(', ') }}</td>
                        <td>{{ optional($user->creator)->name ?? '—' }}</td>
                        <td>{{ optional($user->updater)->name ?? '—' }}</td>
                        <td>{{ optional($user->deleter)->name ?? '—' }}</td>
                        <td>
                            @if($user->trashed())
                                <span class="badge bg-danger">Eliminado</span>
                            @else
                                <span class="badge bg-success">Activo</span>
                            @endif
                        </td>
                        <td>
                            @if(!$user->trashed())
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Editar</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Restaurar este usuario?');">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Restaurar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
