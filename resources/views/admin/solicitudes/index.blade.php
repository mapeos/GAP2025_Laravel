@extends('template.base')

@section('title', 'Solicitudes de Inscripción')
@section('title-page', 'Gestión de Solicitudes de Inscripción')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="ri-user-add-line me-2"></i> Solicitudes de Inscripción</h4>
        </div>
        <div class="card-body">
            <!-- Filtros con botones -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="fw-bold me-3">Filtrar por estado:</span>
                        <button class="btn btn-outline-primary btn-sm filtro-btn active" data-estado="">
                            <i class="ri-list-check me-1"></i> Todas
                        </button>
                        <button class="btn btn-outline-warning btn-sm filtro-btn" data-estado="pendiente">
                            <i class="ri-time-line me-1"></i> Pendientes
                        </button>
                        <button class="btn btn-outline-success btn-sm filtro-btn" data-estado="activo">
                            <i class="ri-check-line me-1"></i> Aceptadas
                        </button>
                        <button class="btn btn-outline-danger btn-sm filtro-btn" data-estado="rechazado">
                            <i class="ri-close-line me-1"></i> Rechazadas
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="tabla-solicitudes-container">
                @include('admin.solicitudes._tabla_paginada', ['solicitudes' => $solicitudes])
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="/admin/js/admin-solicitudes-pagination.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filtroBtns = document.querySelectorAll('.filtro-btn');
            const container = document.getElementById('tabla-solicitudes-container');
            
            function aplicarFiltros(estado) {
                let url = '{{ route("admin.solicitudes.index") }}?';
                const params = new URLSearchParams();
                
                if (estado) {
                    params.append('estado', estado);
                }
                
                url += params.toString();
                
                // Mostrar loading
                container.innerHTML = '<div class="text-center py-4"><i class="ri-loader-4-line fs-2 text-primary"></i><p class="mt-2">Cargando...</p></div>';
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error al aplicar filtros:', error);
                    container.innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
                });
            }
            
            // Manejar clicks en los botones de filtro
            filtroBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover clase active de todos los botones
                    filtroBtns.forEach(b => b.classList.remove('active'));
                    
                    // Agregar clase active al botón clickeado
                    this.classList.add('active');
                    
                    // Aplicar filtro
                    const estado = this.getAttribute('data-estado');
                    aplicarFiltros(estado);
                });
            });
        });
    </script>
@endpush

@push('css')
<style>
.filtro-btn {
    transition: all 0.3s ease;
    border-radius: 20px;
    font-weight: 500;
}

.filtro-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filtro-btn.active {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.filtro-btn.active[data-estado=""] {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.filtro-btn.active[data-estado="pendiente"] {
    background-color: var(--bs-warning);
    border-color: var(--bs-warning);
    color: white;
}

.filtro-btn.active[data-estado="activo"] {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
    color: white;
}

.filtro-btn.active[data-estado="rechazado"] {
    background-color: var(--bs-danger);
    border-color: var(--bs-danger);
    color: white;
}

/* Estilos para la tabla */
.table th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.avatar-sm {
    width: 28px;
    height: 28px;
    font-size: 12px;
}

/* Estilos para los botones de acción */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

/* Animaciones para los botones */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Estilos para los badges */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Estilos para el estado vacío */
.text-muted i {
    opacity: 0.5;
}

/* Responsive para móviles */
@media (max-width: 768px) {
    .filtro-btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endpush
