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
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                Añadir Usuario
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">Volver</a>
        </div>
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
                                <div class="form-check form-switch d-flex align-items-center justify-content-center">
                                    <input class="form-check-input user-status-switch" type="checkbox" role="switch" id="switch-{{ $user->id }}" data-id="{{ $user->id }}" {{ $user->status === 'activo' ? 'checked' : '' }} @if($user->trashed()) disabled @endif>
                                    <label class="form-check-label ms-2" for="switch-{{ $user->id }}">
                                        <span class="badge bg-{{ $user->status === 'activo' ? 'success' : 'warning' }}">{{ ucfirst($user->status) }}</span>
                                    </label>
                                </div>
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

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
            <div class="mb-2 mb-md-0 text-muted">
                @if($users->total() > 0)
                    Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} resultados
                @else
                    Sin resultados
                @endif
            </div>
            <div>
                {{ $users->links() }}
            </div>
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

        // Cambio de estado AJAX con switch
        document.querySelectorAll('.user-status-switch').forEach(function(switchEl) {
            switchEl.addEventListener('change', function() {
                let userId = this.dataset.id;
                let checked = this.checked;
                let label = this.closest('.form-check').querySelector('label span');
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
                        label.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        label.className = 'badge bg-' + (data.status === 'activo' ? 'success' : 'warning');
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
