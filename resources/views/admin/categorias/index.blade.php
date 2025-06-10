@extends('template.base')

@section('title', 'Listado de Categorías')
@section('title-sidebar', 'Categorías')
@section('title-page', 'Listado de Categorías')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">Categorías</a></li>
<li class="breadcrumb-item active">Index Categorías</li>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Categorías</h1>

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    <!-- @include('template.partials.alerts') -->

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    <div id="flash-messages">
        @include('template.partials.alerts')
    </div>

    <div class="mb-3">
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary">Crear Nueva Categoría</a>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Ir a Noticias</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark">Ir al Dashboard</a>
    </div>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categorias as $categoria)
            <tr @if ($categoria->trashed()) class="table-danger" @endif>
                <td>
                    {{ $categoria->nombre }}
                    @if ($categoria->trashed())
                    <i class="ri-alert-line text-danger" title="Categoría eliminada"></i>
                    @endif
                </td>
                <td>{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
                <td>
                    @if ($categoria->trashed())
                    <span class="badge bg-danger">Eliminada</span>
                    @else
                    <span class="badge bg-success">Activa</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.3rem; flex-wrap: nowrap; white-space: nowrap;">

                        <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn btn-warning btn-sm" title="Editar">
                            <i class="ri-edit-line"></i>
                        </a>

                        <button class="btn btn-sm toggle-status-btn
                            {{ $categoria->trashed() ? 'btn-success' : 'btn-danger' }}"
                            data-id="{{ $categoria->id }}"
                            data-action="{{ $categoria->trashed() ? 'restore' : 'delete' }}"
                            title="{{ $categoria->trashed() ? 'Publicar' : 'Eliminar' }}">
                            <i class="{{ $categoria->trashed() ? 'ri-upload-2-line' : 'ri-delete-bin-line' }}"></i>
                        </button>

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No hay categorías registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación si usas -->
    {{-- <div class="d-flex justify-content-center">
        {{ $categorias->links() }}
</div> --}}
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-status-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                let categoriaId = this.dataset.id;
                let action = this.dataset.action;
                let buttonEl = this;
                let row = this.closest('tr');

                if (action === 'delete' && !confirm('¿Eliminar esta categoría?')) {
                    return;
                }

                fetch(`/admin/categorias/${categoriaId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            let badge = row.querySelector('td:nth-child(3) .badge');
                            let titleCell = row.querySelector('td:nth-child(1)');
                            let icon = titleCell.querySelector('i.ri-alert-line');

                            if (data.status === 'activa') {
                                badge.textContent = 'Activa';
                                badge.className = 'badge bg-success';
                                buttonEl.innerHTML = '<i class="ri-delete-bin-line"></i>';
                                buttonEl.className = 'btn btn-danger btn-sm toggle-status-btn';
                                buttonEl.dataset.action = 'delete';
                                buttonEl.title = 'Eliminar';
                                row.classList.remove('table-danger');

                                if (icon) {
                                    icon.remove();
                                }

                                showFlashMessage('Categoría publicada correctamente', 'success');
                            } else {
                                badge.textContent = 'Eliminada';
                                badge.className = 'badge bg-danger';
                                buttonEl.innerHTML = '<i class="ri-upload-2-line"></i>';
                                buttonEl.className = 'btn btn-success btn-sm toggle-status-btn';
                                buttonEl.dataset.action = 'restore';
                                buttonEl.title = 'Publicar';
                                row.classList.add('table-danger');

                                if (!icon) {
                                    const newIcon = document.createElement('i');
                                    newIcon.className = 'ri-alert-line text-danger ms-1';
                                    newIcon.title = 'Categoría eliminada';
                                    titleCell.appendChild(newIcon);
                                }

                                showFlashMessage('Categoría eliminada correctamente', 'warning');
                            }
                        }
                    })
                    .catch(() => {
                        showFlashMessage('Ocurrió un error inesperado. Inténtalo de nuevo.', 'danger');
                    });
            });
        });

        function showFlashMessage(message, type = 'success') {
            const icons = {
                success: 'ri-checkbox-circle-fill text-success',
                danger: 'ri-close-circle-fill text-danger',
                warning: 'ri-alert-line text-warning',
                info: 'ri-information-line text-info'
            };

            const flashContainer = document.getElementById('flash-messages');
            if (!flashContainer) return;

            const wrapper = document.createElement('div');
            wrapper.className = `alert alert-${type} d-flex align-items-center alert-dismissible fade show mt-2`;
            wrapper.setAttribute('role', 'alert');

            wrapper.innerHTML = `
                <i class="${icons[type] || icons.success} me-2 fs-4"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            `;

            flashContainer.innerHTML = '';
            flashContainer.appendChild(wrapper);
        }
    });
</script>
@endpush