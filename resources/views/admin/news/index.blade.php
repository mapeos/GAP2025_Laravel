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

    {{-- Este contenedor es importante para inyectar dinámicamente --}}
    <div id="flash-messages">
        @include('template.partials.alerts')
    </div>

    <h1 class="mb-4">Listado de Noticias</h1>

    <div class="mb-3">
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">Crear Nueva Noticia</a>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary">Ir a Categorías</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark">Ir al Dashboard</a>

    </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-status-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                let newsId = this.dataset.id;
                let action = this.dataset.action;
                let buttonEl = this;
                let row = this.closest('tr');

                if (action === 'delete' && !confirm('¿Estás seguro de que quieres eliminar esta noticia?')) {
                    return;
                }

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

            flashContainer.innerHTML = ''; // Limpia anteriores
            flashContainer.appendChild(wrapper);
        }
    });
</script>
@endpush