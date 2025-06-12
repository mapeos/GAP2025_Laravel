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

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    <!-- @include('template.partials.alerts') -->

    <div class="page-header-container mb-4 border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1 class="h3 mb-0">Listado de Categorías</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line"></i> Nueva Categoría
                </a>
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-newspaper-line"></i> Noticias
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm">
                    <i class="ri-dashboard-line"></i> Dashboard
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Categorías</li>
            </ol>
        </nav>
    </div>

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    <div id="flash-messages">
        @include('template.partials.alerts')
    </div>

    <div class="mb-3 d-flex gap-3">
        <div class="btn-group" role="group" aria-label="Filtrar por estado">
            <button type="button" class="btn btn-outline-secondary btn-sm filter-status active" data-status="all">
                <i class="ri-list-check"></i> Todas
            </button>
            <button type="button" class="btn btn-outline-success btn-sm filter-status" data-status="active">
                <i class="ri-check-line"></i> Publicadas
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm filter-status" data-status="deleted">
                <i class="ri-delete-bin-line"></i> Dadas de baja
            </button>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner" style="display: none;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 mb-0" id="spinnerText" style="font-size: 1.2rem;">Cargando...</p>
        </div>
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

    {{-- Paginación si usas --}}
    <div class="d-flex justify-content-center">
        {{ $categorias->links() }}
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejador para los botones de filtro por estado
        document.querySelectorAll('.filter-status').forEach(button => {
            button.addEventListener('click', function() {
                // Remover clase active de todos los botones
                document.querySelectorAll('.filter-status').forEach(btn => btn.classList.remove('active'));
                // Añadir clase active al botón clickeado
                this.classList.add('active');

                const status = this.dataset.status;
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    if (status === 'all') {
                        row.style.display = '';
                    } else if (status === 'active') {
                        row.style.display = row.classList.contains('table-danger') ? 'none' : '';
                    } else if (status === 'deleted') {
                        row.style.display = row.classList.contains('table-danger') ? '' : 'none';
                    }
                });
            });
        });

        // Código existente para toggle-status-btn
        document.querySelectorAll('.toggle-status-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                let categoriaId = this.dataset.id;
                let action = this.dataset.action;
                let buttonEl = this;
                let row = this.closest('tr');
                let spinner = document.getElementById('loadingSpinner');
                let spinnerText = document.getElementById('spinnerText');

                if (action === 'delete' && !confirm('¿Eliminar esta categoría?')) {
                    return;
                }

                if (action === 'delete') {
                    spinnerText.textContent = 'Eliminando categoría...';
                } else if (action === 'restore') {
                    spinnerText.textContent = 'Publicando categoría...';
                } else {
                    spinnerText.textContent = 'Cargando...';
                }
                spinner.style.display = 'flex';

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
                                // Cambios visuales para "activa"
                                badge.textContent = 'Activa';
                                badge.className = 'badge bg-success';
                                buttonEl.innerHTML = '<i class="ri-delete-bin-line"></i>'; // Para eliminar
                                buttonEl.className = 'btn btn-danger btn-sm toggle-status-btn';
                                buttonEl.dataset.action = 'delete';
                                buttonEl.title = 'Eliminar';
                                row.classList.remove('table-danger');

                                // Elimina el ícono si existe
                                if (icon) {
                                    icon.remove();
                                }

                                showFlashMessage('Categoría publicada correctamente', 'success');
                            } else {
                                // Cambios visuales para "eliminada"
                                badge.textContent = 'Eliminada';
                                badge.className = 'badge bg-danger';
                                buttonEl.innerHTML = '<i class="ri-upload-2-line"></i>'; // Para publicar
                                buttonEl.className = 'btn btn-success btn-sm toggle-status-btn';
                                buttonEl.dataset.action = 'restore';
                                buttonEl.title = 'Publicar';
                                row.classList.add('table-danger');

                                // Añade el ícono si no existe
                                if (!icon) {
                                    const newIcon = document.createElement('i');
                                    newIcon.className = 'ri-alert-line text-danger ms-1';
                                    newIcon.title = 'Categoría eliminada';
                                    titleCell.appendChild(newIcon);
                                }

                                showFlashMessage('Categoría eliminada correctamente', 'warning');
                            }

                            // Actualizar visibilidad según el filtro activo
                            const activeFilterButton = document.querySelector('.filter-status.active');
                            if (activeFilterButton) {
                                activeFilterButton.click();
                            }
                        }
                    })
                    .catch(() => {
                        showFlashMessage('Ocurrió un error inesperado. Inténtalo de nuevo.', 'danger');
                    })
                    .finally(() => {
                        spinner.style.display = 'none';
                    });
            });
        });

        // Manejador para los mensajes flash
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

            flashContainer.innerHTML = ''; // Limpia anteriores
            flashContainer.appendChild(wrapper);
        }
    });
</script>
@endpush