@extends('template.base-admin')

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

        <div class="mb-3">
            <label for="filter-status" class="form-label">Filtrar por estado:</label>
            <select id="filter-status" class="form-select" style="width:auto;display:inline-block">
                <option value="">Todos</option>
                <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activos</option>
                <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
            </select>
        </div>

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
                @forelse ($users as $user)
                    <tr @if($user->trashed()) class="table-secondary" @endif>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <select class="form-select form-select-sm user-role-select" data-id="{{ $user->id }}" @if($user->trashed()) disabled @endif>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->getRoleNames()->contains($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{ optional($user->creator)->name ?? '—' }}</td>
                        <td>{{ optional($user->updater)->name ?? '—' }}</td>
                        <td>{{ optional($user->deleter)->name ?? '—' }}</td>
                        <td>
                            @if($user->trashed())
                                <span class="badge bg-danger">Eliminado</span>
                            @else
                                <span class="badge status-badge bg-{{ $user->status === 'activo' ? 'success' : 'warning' }}" data-id="{{ $user->id }}" style="cursor:pointer">
                                    {{ ucfirst($user->status) }}
                                </span>
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

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtro de estado
        document.getElementById('filter-status').addEventListener('change', function() {
            let status = this.value;
            let url = new URL(window.location.href);
            if(status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            window.location.href = url.toString();
        });

        // Cambio de estado AJAX
        document.querySelectorAll('.status-badge').forEach(function(badge) {
            badge.addEventListener('click', function() {
                let userId = this.dataset.id;
                let badgeEl = this;
                fetch(`/admin/users/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status) {
                        badgeEl.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        badgeEl.classList.toggle('bg-success');
                        badgeEl.classList.toggle('bg-warning');
                    }
                });
            });
        });

        // Cambio de rol AJAX
        document.querySelectorAll('.user-role-select').forEach(function(select) {
            select.addEventListener('change', function() {
                let userId = this.dataset.id;
                let newRole = this.value;
                fetch(`/admin/users/${userId}/change-role`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ role: newRole })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Opcional: feedback visual
                        select.classList.add('border-success');
                        setTimeout(() => select.classList.remove('border-success'), 1000);
                    }
                });
            });
        });
    });
</script>
@endpush
