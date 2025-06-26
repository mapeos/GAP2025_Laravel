@extends('template.base')

@section('title', 'Listado de Cursos')
@section('title-sidebar', 'Cursos')
@section('title-page', 'Listado de Cursos')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">Cursos</a></li>
<li class="breadcrumb-item active">Index Cursos</li>
@endsection

@section('content')
<div class="container">
    {{-- HEADER CON BOTONES --}}
    <div class="page-header-container mb-4 border-bottom pb-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h1 class="h3 mb-0">Listado de Cursos</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary btn-sm">
                    <i class="ri-add-line"></i> Crear Curso
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm">
                    <i class="ri-dashboard-line"></i> Dashboard
                </a>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Cursos</li>
            </ol>
        </nav>
    </div>

    {{-- Contenedor para mensajes flash --}}
    <div id="flash-messages">
        @include('template.partials.alerts')
    </div>

    {{-- FILTROS --}}
    <div class="mb-3 d-flex gap-3">
        {{-- Filtro por estado --}}
        <div class="btn-group" role="group" aria-label="Filtrar por estado">
            <button type="button" class="btn btn-outline-secondary btn-sm filter-status active" data-status="all">
                <i class="ri-list-check"></i> Todos
            </button>
            <button type="button" class="btn btn-outline-success btn-sm filter-status" data-status="active">
                <i class="ri-check-line"></i> Publicados
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm filter-status" data-status="deleted">
                <i class="ri-delete-bin-line"></i> Dados de baja
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
                <i class="ri-arrow-down-line"></i> Más antiguos
            </button>
        </div>

        {{-- Botón de búsqueda avanzada --}}
        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#searchFilters" aria-expanded="false" aria-controls="searchFilters" id="toggleFiltersBtn">
            <i class="ri-filter-3-line"></i> Búsqueda Avanzada
        </button>
    </div>

    {{-- Búsqueda avanzada (colapsable) --}}
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
                        <label for="searchText" class="form-label">Buscar en título y descripción</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchText" placeholder="Escriba para buscar...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Filtro por estado --}}
                    <div class="col-md-4 mb-3">
                        <label for="statusFilter" class="form-label">Filtrar por estado</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activos</option>
                            <option value="inactivo">Inactivos</option>
                        </select>
                    </div>

                    {{-- Filtro por rango de fechas --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Rango de fechas de inicio</label>
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

  {{-- Tabla de cursos --}}
<table class="table align-middle table-responsive">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Plazas</th>
            <th>Estado</th>
            <th>Eliminado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cursos as $curso)
            <tr @if ($curso->trashed()) class="table-danger" @endif>
                <td>{{ $curso->id }}</td>
                <td>
                    <a href="{{ route('admin.cursos.show', $curso->id) }}" class="fw-bold text-decoration-none {{ $curso->trashed() ? 'text-danger' : '' }}">
                        {{ $curso->titulo }}
                    </a>
                    @if ($curso->trashed())
                        <i class="ri-alert-line text-danger" title="Curso eliminado"></i>
                    @endif
                </td>
                <td>{{ $curso->fechaInicio }}</td>
                <td>{{ $curso->fechaFin }}</td>
                <td>
                    <div class="{{ $curso->getPlazasColorClass() }}">
                        <strong>{{ $curso->getPlazasDisponibles() }}</strong> / {{ $curso->plazas }}
                    </div>
                    <small class="text-muted">
                        {{ number_format($curso->getPorcentajeOcupacion(), 1) }}% ocupado
                    </small>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="form-check form-switch me-2">
                            <input class="form-check-input toggle-estado" type="checkbox" 
                                   data-curso-id="{{ $curso->id }}" 
                                   {{ $curso->estado === 'activo' ? 'checked' : '' }}
                                   style="cursor: pointer; width: 2.5rem; height: 1.25rem;">
                        </div>
                        <span class="estado-texto fw-medium">
                            {{ $curso->estado === 'activo' ? 'Publicado' : 'Dado de baja' }}
                        </span>
                    </div>
                </td>
                <td>
                    @if ($curso->trashed())
                        {{ $curso->deleted_at->format('d/m/Y H:i') }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 0.3rem; flex-wrap: nowrap; white-space: nowrap;">
                        <a href="{{ route('admin.cursos.edit', $curso->id) }}" 
                           class="btn btn-sm btn-warning" 
                           title="Editar curso">
                            <i class="ri-edit-line"></i>
                        </a>
                        @if($curso->trashed())
                            <button class="btn btn-sm btn-success toggle-delete" 
                                    data-curso-id="{{ $curso->id }}" 
                                    title="Activar curso">
                                <i class="ri-upload-2-line"></i>
                            </button>
                        @else
                            <button class="btn btn-sm btn-danger toggle-delete" 
                                    data-curso-id="{{ $curso->id }}" 
                                    title="Eliminar curso">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">No hay cursos disponibles.</td>
            </tr>
        @endforelse
    </tbody>
</table>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $cursos->links() }}
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // MANEJADOR DE FILTROS POR ESTADO
    // ========================================
    document.querySelectorAll('.filter-status').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.filter-status').forEach(btn => btn.classList.remove('active'));
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

    // ========================================
    // MANEJADOR DE ORDENAMIENTO POR FECHA
    // ========================================
    document.querySelectorAll('.sort-date').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.sort-date').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const sort = this.dataset.sort;
            const tbody = document.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            if (sort !== 'all') {
                rows.sort((a, b) => {
                    const dateTextA = a.querySelector('td:nth-child(4)').textContent.trim();
                    const dateTextB = b.querySelector('td:nth-child(4)').textContent.trim();
                    
                    const dateObjA = new Date(dateTextA);
                    const dateObjB = new Date(dateTextB);

                    return sort === 'newest' ? dateObjB - dateObjA : dateObjA - dateObjB;
                });

                rows.forEach(row => tbody.appendChild(row));
            } else {
                window.location.reload();
            }
        });
    });

    // ========================================
    // TOGGLE DE ESTADO (PUBLICADO/DADO DE BAJA)
    // ========================================
    document.querySelectorAll('.toggle-estado').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const cursoId = this.dataset.cursoId;
            const isChecked = this.checked;
            const estadoTexto = this.parentElement.parentElement.querySelector('.estado-texto');
            const spinner = document.getElementById('loadingSpinner');
            const spinnerText = document.getElementById('spinnerText');
            
            spinnerText.textContent = isChecked ? 'Publicando curso...' : 'Dando de baja curso...';
            spinner.style.display = 'flex';
            
            fetch(`/admin/cursos/${cursoId}/toggle-estado`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    estado: isChecked ? 'activo' : 'inactivo'
                })
            })
            .then(response => response.json())
            .then(data => {
                spinner.style.display = 'none';
                
                if (data.success) {
                    estadoTexto.textContent = isChecked ? 'Publicado' : 'Dado de baja';
                    
                    if (isChecked) {
                        estadoTexto.classList.add('activo');
                    } else {
                        estadoTexto.classList.remove('activo');
                    }
                    
                    showFlashMessage('success', data.message);
                } else {
                    this.checked = !isChecked;
                    showFlashMessage('error', data.message || 'Error al cambiar el estado');
                }
            })
            .catch(error => {
                spinner.style.display = 'none';
                this.checked = !isChecked;
                showFlashMessage('error', 'Error de conexión');
                console.error('Error:', error);
            });
        });
    });

    // ========================================
    // TOGGLE DE ELIMINACIÓN (SOFT DELETE)
    // ========================================
    document.querySelectorAll('.toggle-delete').forEach(function(button) {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            const isDeleted = this.classList.contains('btn-success');
            const action = isDeleted ? 'restore' : 'delete';
            const row = this.closest('tr');
            const spinner = document.getElementById('loadingSpinner');
            const spinnerText = document.getElementById('spinnerText');
            
            const confirmMessage = isDeleted 
                ? '¿Estás seguro de que quieres restaurar este curso?' 
                : '¿Estás seguro de que quieres eliminar este curso?';
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            spinnerText.textContent = isDeleted ? 'Restaurando curso...' : 'Eliminando curso...';
            spinner.style.display = 'flex';
            
            fetch(`/admin/cursos/${cursoId}/${action}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                spinner.style.display = 'none';
                
                if (data.success) {
                    if (isDeleted) {
                        // Restaurar curso
                        row.classList.remove('table-danger');
                        button.className = 'btn btn-sm btn-danger toggle-delete';
                        button.innerHTML = '<i class="ri-delete-bin-line"></i>';
                        button.title = 'Eliminar curso';
                        
                        const fechaCell = row.querySelector('td:nth-child(8)');
                        if (fechaCell) {
                            fechaCell.innerHTML = '<span class="text-muted">-</span>';
                        }
                        
                        const titleCell = row.querySelector('td:nth-child(2)');
                        const alertIcon = titleCell.querySelector('.ri-alert-line');
                        if (alertIcon) {
                            alertIcon.remove();
                        }
                    } else {
                        // Eliminar curso
                        row.classList.add('table-danger');
                        button.className = 'btn btn-sm btn-success toggle-delete';
                        button.innerHTML = '<i class="ri-upload-2-line"></i>';
                        button.title = 'Restaurar curso';
                        
                        const fechaCell = row.querySelector('td:nth-child(8)');
                        if (fechaCell) {
                            const now = new Date();
                            const fecha = now.toLocaleDateString('es-ES') + ' ' + now.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
                            fechaCell.innerHTML = fecha;
                        }
                        
                        const titleCell = row.querySelector('td:nth-child(2)');
                        const alertIcon = document.createElement('i');
                        alertIcon.className = 'ri-alert-line text-danger ms-1';
                        alertIcon.title = 'Curso eliminado';
                        titleCell.appendChild(alertIcon);
                    }
                    
                    showFlashMessage('success', data.message);
                } else {
                    showFlashMessage('error', data.message || 'Error al procesar la acción');
                }
            })
            .catch(error => {
                spinner.style.display = 'none';
                showFlashMessage('error', 'Error de conexión');
                console.error('Error:', error);
            });
        });
    });

    // ========================================
    // FUNCIÓN PARA MOSTRAR MENSAJES FLASH
    // ========================================
    function showFlashMessage(type, message) {
        const icons = {
            success: 'ri-checkbox-circle-fill text-success',
            error: 'ri-close-circle-fill text-danger',
            warning: 'ri-alert-line text-warning',
            info: 'ri-information-line text-info'
        };

        const flashContainer = document.getElementById('flash-messages');
        if (!flashContainer) return;

        const wrapper = document.createElement('div');
        wrapper.className = `alert alert-${type === 'error' ? 'danger' : type} d-flex align-items-center alert-dismissible fade show mt-2`;
        wrapper.setAttribute('role', 'alert');

        wrapper.innerHTML = `
            <i class="${icons[type] || icons.success} me-2 fs-4"></i>
            <div>${message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        `;

        flashContainer.innerHTML = '';
        flashContainer.appendChild(wrapper);
    }

    // ========================================
    // FILTROS DE BÚSQUEDA AVANZADA
    // ========================================
    const searchText = document.getElementById('searchText');
    const statusFilter = document.getElementById('statusFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const clearFilters = document.getElementById('clearFilters');
    const clearSearch = document.getElementById('clearSearch');
    const filterResults = document.getElementById('filterResults');

    function applyAllFilters() {
        const searchValue = searchText.value.toLowerCase().trim();
        const statusValue = statusFilter.value.toLowerCase();
        const dateFromValue = dateFrom.value;
        const dateToValue = dateTo.value;

        const rows = document.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            let showRow = true;

            // Filtro por texto
            if (searchValue) {
                const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const description = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                if (!title.includes(searchValue) && !description.includes(searchValue)) {
                    showRow = false;
                }
            }

            // Filtro por estado
            if (statusValue && showRow) {
                const estadoCheckbox = row.querySelector('td:nth-child(7) .toggle-estado');
                const isActivo = estadoCheckbox && estadoCheckbox.checked;
                const currentStatus = isActivo ? 'activo' : 'inactivo';
                
                if (currentStatus !== statusValue) {
                    showRow = false;
                }
            }

            // Filtro por rango de fechas
            if (showRow && (dateFromValue || dateToValue)) {
                const dateText = row.querySelector('td:nth-child(4)').textContent.trim();
                const rowDate = new Date(dateText);

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

            if (showRow) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const totalRows = rows.length;
        if (searchValue || statusValue || dateFromValue || dateToValue) {
            filterResults.textContent = `Mostrando ${visibleCount} de ${totalRows} cursos`;
        } else {
            filterResults.textContent = '';
        }
    }

    // ========================================
    // EVENT LISTENERS PARA FILTROS
    // ========================================
    clearFilters.addEventListener('click', function() {
        searchText.value = '';
        statusFilter.value = '';
        dateFrom.value = '';
        dateTo.value = '';
        filterResults.textContent = '';
        
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.style.display = '';
        });
    });

    clearSearch.addEventListener('click', function() {
        searchText.value = '';
        applyAllFilters();
    });

    let searchTimeout;
    searchText.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyAllFilters, 300);
    });

    statusFilter.addEventListener('change', applyAllFilters);
    dateFrom.addEventListener('change', applyAllFilters);
    dateTo.addEventListener('change', applyAllFilters);

    // ========================================
    // VALIDACIÓN DE FECHAS
    // ========================================
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

    // ========================================
    // MANEJADOR DEL BOTÓN CERRAR FILTROS
    // ========================================
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

    // ========================================
    // INICIALIZACIÓN DE ESTADOS
    // ========================================
    document.querySelectorAll('.toggle-estado').forEach(function(checkbox) {
        const estadoTexto = checkbox.parentElement.parentElement.querySelector('.estado-texto');
        if (checkbox.checked) {
            estadoTexto.classList.add('activo');
        } else {
            estadoTexto.classList.remove('activo');
        }
    });
});
</script>
@endpush

@push('css')
<style>
    /* ========================================
       ESTILOS PARA EL SWITCH DE ESTADO
       ======================================== */
    
    /* Estilos base del switch */
    .form-check-input.toggle-estado {
        background-color: #e9ecef;
        border-color: #dee2e6;
        transition: all 0.2s ease-in-out;
    }
    
    /* Estado activo (publicado) */
    .form-check-input.toggle-estado:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    /* Efecto de focus */
    .form-check-input.toggle-estado:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    /* ========================================
       ESTILOS PARA EL TEXTO DE ESTADO
       ======================================== */
    
    /* Estilo base del texto */
    .estado-texto {
        font-size: 0.875rem;
        color: #6c757d;
        transition: color 0.2s ease-in-out;
    }
    
    /* Color cuando está activo (publicado) */
    .estado-texto.activo {
        color: #198754;
    }
</style>
@endpush
