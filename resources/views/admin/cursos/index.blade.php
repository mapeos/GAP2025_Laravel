@extends('template.base')

@section('title', 'Cursos')

@section('content')
<h1>Cursos</h1>

<a href="{{ route('admin.cursos.create') }}" class="btn btn-primary mb-3">Crear curso</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Descripción</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Plazas</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cursos as $curso)
        <tr>
            <td>{{ $curso->id }}</td>
            <td>{{ $curso->titulo }}</td>
            <td>{{ $curso->descripcion }}</td>
            <td>{{ $curso->fechaInicio }}</td>
            <td>{{ $curso->fechaFin }}</td>
            <td>{{ $curso->plazas }}</td>
            <td>{{ $curso->estado }}</td>
            <td class="text-center">
                <a href="{{ route('admin.cursos.edit', $curso->id) }}"
                    class="btn btn-warning btn-sm me-1"
                    title="Modificar">
                    <i class="fa fa-edit"></i>
                </a>
                <form action="{{ route('admin.cursos.destroy', $curso->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm me-1" title="Eliminar"
                        onclick="return confirm('¿Seguro que deseas eliminar este curso?')">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
                <a href="{{ route('admin.cursos.show', $curso->id) }}"
                    class="btn btn-info btn-sm"
                    title="Más información">
                    <i class="fa fa-info-circle"></i>
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9">No hay cursos disponibles.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
</div>
@endsection

@push('js')
<script>
    // Inicializar colores de estado al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-estado').forEach(function(checkbox) {
            const estadoTexto = checkbox.parentElement.parentElement.querySelector('.estado-texto');
            if (checkbox.checked) {
                estadoTexto.classList.add('activo');
            } else {
                estadoTexto.classList.remove('activo');
            }
        });
    });

    // Toggle de estado (activo/inactivo) - Cambia el estado del curso
    document.querySelectorAll('.toggle-estado').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const cursoId = this.dataset.cursoId;
            const isChecked = this.checked;
            const estadoTexto = this.parentElement.parentElement.querySelector('.estado-texto');
            const spinner = document.getElementById('loadingSpinner');
            const spinnerText = document.getElementById('spinnerText');

            // Mostrar spinner con texto específico
            if (isChecked) {
                spinnerText.textContent = 'Activando curso...';
            } else {
                spinnerText.textContent = 'Desactivando curso...';
            }
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
                        // Actualizar texto del estado
                        estadoTexto.textContent = isChecked ? 'Activo' : 'Inactivo';

                        // Actualizar color del texto
                        if (isChecked) {
                            estadoTexto.classList.add('activo');
                        } else {
                            estadoTexto.classList.remove('activo');
                        }

                        // Mostrar mensaje de éxito
                        showFlashMessage('success', data.message);
                    } else {
                        // Revertir checkbox si hay error
                        this.checked = !isChecked;
                        showFlashMessage('error', data.message || 'Error al cambiar el estado');
                    }
                })
                .catch(error => {
                    spinner.style.display = 'none';
                    // Revertir checkbox si hay error
                    this.checked = !isChecked;
                    showFlashMessage('error', 'Error de conexión');
                    console.error('Error:', error);
                });
        });
    });

    // Toggle de eliminación (soft delete) - Elimina o restaura el curso
    document.querySelectorAll('.toggle-delete').forEach(function(button) {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            const isDeleted = this.classList.contains('btn-success'); // Si es verde, está eliminado
            const action = isDeleted ? 'restore' : 'delete';
            const row = this.closest('tr');
            const spinner = document.getElementById('loadingSpinner');
            const spinnerText = document.getElementById('spinnerText');

            // Confirmar acción
            const confirmMessage = isDeleted ?
                '¿Estás seguro de que quieres activar este curso?' :
                '¿Estás seguro de que quieres eliminar este curso?';

            if (!confirm(confirmMessage)) {
                return;
            }

            // Mostrar spinner con texto específico
            if (isDeleted) {
                spinnerText.textContent = 'Publicando curso...';
            } else {
                spinnerText.textContent = 'Eliminando curso...';
            }
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
                            // Cambios visuales para "activo"
                            row.classList.remove('table-danger');
                            button.className = 'btn btn-sm btn-danger toggle-delete';
                            button.innerHTML = '<i class="ri-delete-bin-line"></i>';
                            button.title = 'Eliminar curso';

                            // Limpiar fecha de eliminación
                            const fechaCell = row.querySelector('td:nth-child(8)');
                            if (fechaCell) {
                                fechaCell.innerHTML = '<span class="text-muted">-</span>';
                            }

                            // Remover icono de alerta del título
                            const titleCell = row.querySelector('td:nth-child(2)');
                            const alertIcon = titleCell.querySelector('.ri-alert-line');
                            if (alertIcon) {
                                alertIcon.remove();
                            }
                        } else {
                            // Cambios visuales para "eliminado"
                            row.classList.add('table-danger');
                            button.className = 'btn btn-sm btn-success toggle-delete';
                            button.innerHTML = '<i class="ri-upload-2-line"></i>';
                            button.title = 'Activar curso';

                            // Mostrar fecha de eliminación
                            const fechaCell = row.querySelector('td:nth-child(8)');
                            if (fechaCell) {
                                const now = new Date();
                                const fecha = now.toLocaleDateString('es-ES') + ' ' + now.toLocaleTimeString('es-ES', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                fechaCell.innerHTML = fecha;
                            }

                            // Agregar icono de alerta al título
                            const titleCell = row.querySelector('td:nth-child(2)');
                            const alertIcon = document.createElement('i');
                            alertIcon.className = 'ri-alert-line text-danger ms-1';
                            alertIcon.title = 'Curso eliminado';
                            titleCell.appendChild(alertIcon);
                        }

                        // Mostrar mensaje de éxito
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

    // Función auxiliar - Muestra mensajes flash con iconos
    function showFlashMessage(type, message) {
        const container = document.getElementById('flash-messages');
        if (!container) return;

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'ri-checkbox-circle-fill' : 'ri-close-circle-fill';
        const iconColor = type === 'success' ? 'text-success' : 'text-danger';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="${iconClass} ${iconColor} me-2 fs-4"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        container.innerHTML = alertHtml;

    }
</script>
@endpush

@push('css')
<style>
    /* Estilos para el switch de estado - Colores y transiciones */
    .form-check-input.toggle-estado {
        background-color: #e9ecef;
        border-color: #dee2e6;
        transition: all 0.2s ease-in-out;
    }

    .form-check-input.toggle-estado:checked {
        background-color: #198754;
        border-color: #198754;
    }

    .form-check-input.toggle-estado:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    /* Estilos para el texto de estado - Colores dinámicos */
    .estado-texto {
        font-size: 0.875rem;
        color: #6c757d;
        transition: color 0.2s ease-in-out;
    }

    .estado-texto.activo {
        color: #198754;
    }
</style>
@endpush