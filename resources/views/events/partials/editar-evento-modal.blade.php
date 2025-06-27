<!-- Modal Editar Evento -->
<div class="modal fade" id="editarEventoModal" tabindex="-1" aria-labelledby="editarEventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarEventoModalLabel">Editar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editarEventoForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editEventoId" name="evento_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="editTitulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="editTitulo" name="titulo" required>
                            </div>
                        </div>
                        @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="editTipoEventoId" class="form-label">Tipo de Evento *</label>
                                <select class="form-select" id="editTipoEventoId" name="tipo_evento_id" required>
                                    <option value="">Seleccionar tipo</option>
                                    @foreach(\App\Models\TipoEvento::where('status', true)->get() as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFechaInicio" class="form-label">Fecha de inicio *</label>
                                <input type="datetime-local" class="form-control" id="editFechaInicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFechaFin" class="form-label">Fecha de fin *</label>
                                <input type="datetime-local" class="form-control" id="editFechaFin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"
                                  placeholder="Descripción del evento..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUbicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="editUbicacion" name="ubicacion"
                                       placeholder="Ubicación física del evento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUrlVirtual" class="form-label">URL Virtual</label>
                                <input type="url" class="form-control" id="editUrlVirtual" name="url_virtual"
                                       placeholder="https://meet.google.com/...">
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Estado</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <button type="button" class="btn btn-danger" onclick="deleteEvento(event)">
                            <i class="ri-delete-bin-line"></i> Eliminar
                        </button>
                        <button type="button" class="btn btn-info" onclick="verDetallesEvento()">
                            <i class="ri-eye-line"></i> Ver Detalles
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Evento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteEvento(event) {
    // Si se proporciona un evento, prevenir el comportamiento predeterminado
    if (event) {
        event.preventDefault();
    }

    const eventoId = document.getElementById('editEventoId').value;
    if (confirm('¿Estás seguro de que quieres eliminar este evento?')) {
        // Añadir parámetro json=true a la URL para forzar respuesta JSON
        fetch(`/admin/events/${eventoId}?json=true`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Primero verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    if (data.success) {
                        // Operación exitosa
                        showNotification('Evento eliminado exitosamente', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('editarEventoModal')).hide();

                        // Cambiar a vista mensual después de eliminar
                        calendar.changeView('dayGridMonth');

                        // Recargar eventos
                        loadEventosAjax();
                    } else {
                        showNotification(data.message || 'Error al eliminar evento', 'error');
                    }
                    return data;
                });
            } else {
                // Si no es JSON, manejar como éxito de todas formas
                showNotification('Evento eliminado exitosamente', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editarEventoModal')).hide();

                // Cambiar a vista mensual después de eliminar
                calendar.changeView('dayGridMonth');

                // Recargar eventos
                loadEventosAjax();

                return { success: true };
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al eliminar evento', 'error');
        });

        // Importante: devolver false para evitar comportamiento predeterminado
        return false;
    }
}

function verDetallesEvento() {
    const eventoId = document.getElementById('editEventoId').value;
    // Cerrar el modal antes de navegar
    bootstrap.Modal.getInstance(document.getElementById('editarEventoModal')).hide();
    // Navegar a la página de detalles
    window.location.href = `/events/${eventoId}`;
}
</script>
