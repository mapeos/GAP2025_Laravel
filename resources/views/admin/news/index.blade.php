@extends('template.base')

@section('title', 'Listado de Noticias')
@section('title-sidebar', 'Noticias')
@section('title-page', 'Listado de Noticias')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">News</a></li>
<li class="breadcrumb-item active">Index News</li>
@endsection

@section('content')
<div class="container">

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    <!-- @include('template.partials.alerts') -->

    <div class="page-header-container mb-4 border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1 class="h3 mb-0">Listado de Noticias</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line"></i> Nueva Noticia
                </a>
                <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ri-price-tag-3-line"></i> Categorías
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm">
                    <i class="ri-dashboard-line"></i> Dashboard
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Noticias</li>
            </ol>
        </nav>
    </div>

    {{-- Este contenedor es importante para inyectar dinámicamente --}}
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

        {{-- Ordenar por fecha --}}
        <div class="btn-group" role="group" aria-label="Ordenar por fecha">
            <button type="button" class="btn btn-outline-secondary btn-sm sort-date active" data-sort="all">
                <i class="ri-time-line"></i> Sin ordenar
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm sort-date" data-sort="newest">
                <i class="ri-arrow-up-line"></i> Más recientes
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm sort-date" data-sort="oldest">
                <i class="ri-arrow-down-line"></i> Más antiguas
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
                    {{-- Búsqueda por texto --}}
                    <div class="col-md-4 mb-3">
                        <label for="searchText" class="form-label">Buscar en título y contenido</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchText" placeholder="Escriba para buscar...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Filtro por categorías --}}
                    <div class="col-md-4 mb-3">
                        <label for="categoryFilter" class="form-label">Filtrar por categoría</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias ?? [] as $categoria)
                            <option value="{{ $categoria->nombre }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro por rango de fechas --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rango de fechas</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="dateFrom" placeholder="Desde">
                            <span class="input-group-text">hasta</span>
                            <input type="date" class="form-control" id="dateTo" placeholder="Hasta">
                        </div>
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

    {{-- Tabla de noticias --}}
    <table class="table align-middle table-responsive">
        <thead>
            <tr>
                <th>Título</th>
                <th style="width: 200px;">Categorías</th>
                <th>Autor</th>
                <th>Publicada</th>
                <th>Modificada</th>
                <th>Eliminada</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($news as $item)
            <tr @if ($item->trashed()) class="table-danger" @endif>
                <td>
                    {{ $item->titulo }}
                    @if ($item->trashed())
                    <i class="ri-alert-line text-danger" title="Noticia eliminada"></i>
                    @endif
                </td>
                <td style="white-space: normal; overflow-wrap: break-word;">
                    @if ($item->categorias->isNotEmpty())
                    @foreach ($item->categorias as $categoria)
                    <span class="badge bg-info text-dark me-1 mb-1">{{ $categoria->nombre }}</span>
                    @endforeach
                    @else
                    <span class="text-muted">Pendiente</span>
                    @endif
                </td>
                <td>{{ $item->autor ?? 'Sin autor' }}</td>
                <td>{{ $item->fecha_publicacion->format('d/m/Y H:i') }}</td>
                <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>

                <td>
                    @if ($item->trashed())
                    {{ $item->deleted_at->format('d/m/Y H:i') }}
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>

                <td>
                    @if ($item->trashed())
                    <span class="badge bg-danger">Dada de baja</span>
                    @else
                    <span class="badge bg-success">Publicada</span>
                    @endif
                </td>

                <td>
                    <div style="display: flex; gap: 0.3rem; flex-wrap: nowrap; white-space: nowrap;">

                        <a href="{{ route('admin.news.show', $item) }}" class="btn btn-info btn-sm" title="Ver">
                            <i class="ri-eye-line"></i>
                        </a>

                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-warning btn-sm" title="Editar">
                            <i class="ri-edit-line"></i>
                        </a>

                        <button class="btn btn-sm toggle-status-btn 
                {{ $item->trashed() ? 'btn-success' : 'btn-danger' }}"
                            data-id="{{ $item->id }}"
                            data-action="{{ $item->trashed() ? 'restore' : 'delete' }}"
                            title="{{ $item->trashed() ? 'Publicar' : 'Eliminar' }}">
                            <i class="{{ $item->trashed() ? 'ri-upload-2-line' : 'ri-delete-bin-line' }}"></i>
                        </button>

                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No hay noticias registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>
@endsection

@push('js')
<script>
    // Función para manejar el toggle de estado
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

        // Manejador para los botones de ordenamiento por fecha
        document.querySelectorAll('.sort-date').forEach(button => {
            button.addEventListener('click', function() {
                // Remover clase active de todos los botones
                document.querySelectorAll('.sort-date').forEach(btn => btn.classList.remove('active'));
                // Añadir clase active al botón clickeado
                this.classList.add('active');

                const sort = this.dataset.sort;
                const tbody = document.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                if (sort !== 'all') {
                    rows.sort((a, b) => {
                        // Obtener el texto de la fecha de publicación (4ta columna)
                        const dateTextA = a.querySelector('td:nth-child(4)').textContent.trim();
                        const dateTextB = b.querySelector('td:nth-child(4)').textContent.trim();
                        
                        // Convertir el formato dd/mm/yyyy HH:ii a un objeto Date
                        const [dateA, timeA] = dateTextA.split(' ');
                        const [dayA, monthA, yearA] = dateA.split('/');
                        const [hoursA, minutesA] = timeA.split(':');
                        
                        const [dateB, timeB] = dateTextB.split(' ');
                        const [dayB, monthB, yearB] = dateB.split('/');
                        const [hoursB, minutesB] = timeB.split(':');

                        const dateObjA = new Date(yearA, monthA - 1, dayA, hoursA, minutesA);
                        const dateObjB = new Date(yearB, monthB - 1, dayB, hoursB, minutesB);

                        return sort === 'newest' ? dateObjB - dateObjA : dateObjA - dateObjB;
                    });

                    // Limpiar y reordenar las filas
                    rows.forEach(row => tbody.appendChild(row));
                } else {
                    // Recargar la página para restaurar el orden original
                    window.location.reload();
                }
            });
        });

        // Código existente para toggle-status-btn
        document.querySelectorAll('.toggle-status-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                let newsId = this.dataset.id;
                let action = this.dataset.action;
                let buttonEl = this;
                let row = this.closest('tr');
                let spinner = document.getElementById('loadingSpinner');
                let spinnerText = document.getElementById('spinnerText');

                if (action === 'delete' && !confirm('¿Estás seguro de que quieres eliminar esta noticia?')) {
                    return;
                }

                if (action === 'delete') {
                    spinnerText.textContent = 'Eliminando noticia...';
                } else if (action === 'restore') {
                    spinnerText.textContent = 'Publicando noticia...';
                } else {
                    spinnerText.textContent = 'Cargando...';
                }
                spinner.style.display = 'flex';

                fetch(`/admin/news/${newsId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            // Obtiene los elementos afectados
                            let badge = row.querySelector('td:nth-child(7) .badge');
                            let deletedAtCell = row.querySelector('td:nth-child(6)');
                            let titleCell = row.querySelector('td:nth-child(1)');
                            let icon = titleCell.querySelector('i.ri-alert-line');

                            if (data.status === 'publicada') {
                                // Cambios visuales para "publicada"
                                badge.textContent = 'Publicada';
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

                                // Actualiza la columna de "Eliminada"
                                deletedAtCell.innerHTML = '<span class="text-muted">-</span>';

                                showFlashMessage('Noticia publicada correctamente', 'success');
                            } else {
                                // Cambios visuales para "eliminada"
                                badge.textContent = 'Dada de baja';
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
                                    newIcon.title = 'Noticia eliminada';
                                    titleCell.appendChild(newIcon);
                                }

                                // Actualiza la columna con la nueva fecha
                                const now = new Date();
                                const formatted = now.toLocaleDateString('es-ES') + ' ' + now.toLocaleTimeString('es-ES', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                deletedAtCell.textContent = formatted;

                                showFlashMessage('Noticia eliminada correctamente', 'warning');
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

        // Función para mostrar mensajes de flash
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

        // NUEVOS FILTROS: Funcionalidad de búsqueda avanzada
        const searchText = document.getElementById('searchText');
        const categoryFilter = document.getElementById('categoryFilter');
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');
        const clearFilters = document.getElementById('clearFilters');
        const clearSearch = document.getElementById('clearSearch');
        const filterResults = document.getElementById('filterResults');

        // Función para aplicar todos los filtros
        function applyAllFilters() {
            const searchValue = searchText.value.toLowerCase().trim();
            const categoryValue = categoryFilter.value.toLowerCase();
            const dateFromValue = dateFrom.value;
            const dateToValue = dateTo.value;

            const rows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                let showRow = true;

                // Filtro por texto (título y contenido)
                if (searchValue) {
                    const title = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    // Nota: El contenido no está visible en la tabla, solo se filtra por título
                    if (!title.includes(searchValue)) {
                        showRow = false;
                    }
                }

                // Filtro por categoría
                if (categoryValue && showRow) {
                    const categories = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    if (!categories.includes(categoryValue)) {
                        showRow = false;
                    }
                }

                // Filtro por rango de fechas
                if (showRow && (dateFromValue || dateToValue)) {
                    const dateText = row.querySelector('td:nth-child(4)').textContent.trim();
                    const [datePart] = dateText.split(' ');
                    const [day, month, year] = datePart.split('/');
                    const rowDate = new Date(year, month - 1, day);

                    if (dateFromValue) {
                        const fromDate = new Date(dateFromValue);
                        if (rowDate < fromDate) {
                            showRow = false;
                        }
                    }

                    if (dateToValue && showRow) {
                        const toDate = new Date(dateToValue);
                        if (rowDate > toDate) {
                            showRow = false;
                        }
                    }
                }

                // Aplicar visibilidad
                if (showRow) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Mostrar resultados del filtro
            const totalRows = rows.length;
            if (searchValue || categoryValue || dateFromValue || dateToValue) {
                filterResults.textContent = `Mostrando ${visibleCount} de ${totalRows} noticias`;
            } else {
                filterResults.textContent = '';
            }
        }

        // Event listeners para los nuevos filtros
        clearFilters.addEventListener('click', function() {
            // Limpiar todos los campos de filtro
            searchText.value = '';
            categoryFilter.value = '';
            dateFrom.value = '';
            dateTo.value = '';
            filterResults.textContent = '';
            
            // Mostrar todas las filas
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        });

        clearSearch.addEventListener('click', function() {
            searchText.value = '';
            applyAllFilters();
        });

        // Búsqueda en tiempo real (con debounce)
        let searchTimeout;
        searchText.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyAllFilters, 300);
        });

        // Filtros que se aplican automáticamente
        categoryFilter.addEventListener('change', applyAllFilters);
        dateFrom.addEventListener('change', applyAllFilters);
        dateTo.addEventListener('change', applyAllFilters);

        // Validación de fechas
        dateFrom.addEventListener('change', function() {
            if (dateTo.value && this.value > dateTo.value) {
                dateTo.value = this.value;
            }
        });

        dateTo.addEventListener('change', function() {
            if (dateFrom.value && this.value < dateFrom.value) {
                dateFrom.value = this.value;
            }
        });

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