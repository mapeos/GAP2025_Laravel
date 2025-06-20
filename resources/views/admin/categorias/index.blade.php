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

    {{-- FILTROS EXISTENTES --}}
    <div class="mb-3 d-flex gap-3">
        {{-- Filtro por estado --}}
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

        {{-- Botón de búsqueda avanzada --}}
        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters" id="toggleFiltersBtn">
            <i class="ri-filter-3-line"></i> Búsqueda Avanzada
        </button>
    </div>

    {{-- NUEVOS FILTROS: Búsqueda avanzada (colapsable) --}}
    <div class="collapse mb-3" id="searchFilters">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Búsqueda Avanzada</h6>
                <button type="button" class="btn-close" id="closeFiltersBtn" aria-label="Cerrar"></button>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Búsqueda por nombre --}}
                    <div class="col-md-6 mb-3">
                        <label for="searchName" class="form-label">Buscar por nombre</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchName" placeholder="Escriba el nombre de la categoría...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Ordenamiento simplificado --}}
                    <div class="col-md-6 mb-3">
                        <label for="sortOrder" class="form-label">Ordenar por</label>
                        <select class="form-select" id="sortOrder">
                            <option value="default">Todas</option>
                            <option value="name-asc">A-Z</option>
                            <option value="name-desc">Z-A</option>
                        </select>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilters">
                            <i class="ri-refresh-line"></i> Limpiar Filtros
                        </button>
                        <span class="text-muted ms-2" id="filterResults"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Spinner de carga --}}
    <div class="loading-spinner" id="loadingSpinner" style="display: none;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 mb-0" id="spinnerText" style="font-size: 1.2rem;">Cargando...</p>
        </div>
    </div>

    {{-- Tabla de categorías --}}
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

        // NUEVOS FILTROS: Funcionalidad de búsqueda y ordenamiento para categorías
        const searchName = document.getElementById('searchName');
        const sortOrder = document.getElementById('sortOrder');
        const clearFilters = document.getElementById('clearFilters');
        const clearSearch = document.getElementById('clearSearch');
        const filterResults = document.getElementById('filterResults');

        // Función para aplicar filtros y ordenamiento
        function applyCategoryFilters() {
            const searchValue = searchName.value.toLowerCase().trim();
            const sortValue = sortOrder.value;

            const rows = document.querySelectorAll('tbody tr');

            // Filtrar por nombre
            rows.forEach(row => {
                let showRow = true;
                
                if (searchValue) {
                    const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    if (!name.includes(searchValue)) {
                        showRow = false;
                    }
                }

                // Aplicar visibilidad
                if (showRow) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Ordenar las filas visibles
            if (sortValue !== 'default') {
                const tbody = document.querySelector('tbody');
                const visibleRows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
                
                visibleRows.sort((a, b) => {
                    const nameA = a.querySelector('td:nth-child(1)').textContent.toLowerCase().trim();
                    const nameB = b.querySelector('td:nth-child(1)').textContent.toLowerCase().trim();

                    if (sortValue === 'name-asc') {
                        return nameA.localeCompare(nameB, 'es');
                    } else if (sortValue === 'name-desc') {
                        return nameB.localeCompare(nameA, 'es');
                    }
                    return 0;
                });

                // Reordenar solo las filas visibles
                visibleRows.forEach(row => {
                    tbody.appendChild(row);
                });
            }

            // Mostrar resultados del filtro
            const visibleCount = document.querySelectorAll('tbody tr:not([style*="display: none"])').length;
            const totalRows = rows.length;
            
            if (searchValue || sortValue !== 'default') {
                filterResults.textContent = `Mostrando ${visibleCount} de ${totalRows} categorías`;
            } else {
                filterResults.textContent = '';
            }
        }

        // Event listeners para los nuevos filtros
        clearFilters.addEventListener('click', function() {
            // Limpiar todos los campos de filtro
            searchName.value = '';
            sortOrder.value = 'default';
            filterResults.textContent = '';
            
            // Mostrar todas las filas
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        });

        clearSearch.addEventListener('click', function() {
            searchName.value = '';
            applyCategoryFilters();
        });

        // Búsqueda en tiempo real (con debounce)
        let searchTimeout;
        searchName.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyCategoryFilters, 300);
        });

        // Ordenamiento automático
        sortOrder.addEventListener('change', applyCategoryFilters);

        // Hacer que la X funcione correctamente
        const closeButton = document.getElementById('closeFiltersBtn');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                const searchFilters = document.getElementById('searchFilters');
                if (searchFilters) {
                    const bsCollapse = new bootstrap.Collapse(searchFilters);
                    bsCollapse.hide();
                }
            });
        }
    });
</script>
@endpush