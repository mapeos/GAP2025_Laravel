<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    <div id="calendar"></div>

    <div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="eventoModalLabel">Detalles del evento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form id="formEditarEvento">
              <input type="hidden" id="eventoId">
              <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" required>
              </div>
              <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion"></textarea>
              </div>
              <!-- Puedes agregar más campos si lo necesitas -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnEliminar" class="btn btn-danger">Eliminar</button>
            <button type="button" id="btnGuardar" class="btn btn-primary">Guardar cambios</button>
          </div>
        </div>
      </div>
    </div>

    <!-- FullCalendar CSS -->
    <!-- FullCalendar JS -->
    <!-- Bootstrap JS para el modal -->
    <script>
        window.eventosData = @json($eventos ?? []);
        window.initDashboardCalendar = function() {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;
            // Destruir calendario anterior si existe
            if (window.dashboardCalendar) {
                window.dashboardCalendar.destroy();
            }
            // Esperar a que FullCalendar esté disponible
            if (typeof FullCalendar === 'undefined') {
                setTimeout(window.initDashboardCalendar, 100);
                return;
            }
            window.dashboardCalendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: window.eventosData,
                editable: true,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    document.getElementById('eventoId').value = info.event.id;
                    document.getElementById('titulo').value = info.event.title;
                    document.getElementById('descripcion').value = info.event.extendedProps.descripcion || '';
                    var modal = new bootstrap.Modal(document.getElementById('eventoModal'));
                    modal.show();
                },
                eventDrop: function(info) {
                    fetch(`/admin/eventos/${info.event.id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            fecha_inicio: info.event.start.toISOString().slice(0, 19).replace('T', ' '),
                            fecha_fin: info.event.end
                                ? info.event.end.toISOString().slice(0, 19).replace('T', ' ')
                                : info.event.start.toISOString().slice(0, 19).replace('T', ' ')
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Error al actualizar la fecha del evento.');
                            info.revert();
                        }
                    })
                    .catch(() => {
                        alert('Error de conexión.');
                        info.revert();
                    });
                }
            });
            window.dashboardCalendar.render();

            document.getElementById('btnEliminar').onclick = function() {
                let id = document.getElementById('eventoId').value;
                if (confirm('¿Seguro que deseas eliminar este evento?')) {
                    fetch(`/admin/eventos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            var modal = bootstrap.Modal.getInstance(document.getElementById('eventoModal'));
                            modal.hide();
                            window.dashboardCalendar.getEventById(id).remove();
                            alert('Evento eliminado exitosamente.');
                        } else {
                            alert('Error al eliminar el evento.');
                        }
                    });
                }
            };

            document.getElementById('btnGuardar').onclick = function() {
                let id = document.getElementById('eventoId').value;
                let titulo = document.getElementById('titulo').value;
                let descripcion = document.getElementById('descripcion').value;
                fetch(`/admin/eventos/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        titulo: titulo,
                        descripcion: descripcion
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('eventoModal'));
                        modal.hide();
                        let event = window.dashboardCalendar.getEventById(id);
                        event.setProp('title', titulo);
                        event.setExtendedProp('descripcion', descripcion);
                        alert('Evento actualizado exitosamente.');
                    } else {
                        alert('Error al actualizar el evento.');
                    }
                });
            };
        };
        // Ejecutar SIEMPRE la inicialización, sin esperar eventos
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initDashboardCalendar);
        } else {
            window.initDashboardCalendar();
        }
        // wire:navigate soporte para recarga de scripts
        document.addEventListener('navigate', function() {
            setTimeout(function() {
                window.initDashboardCalendar();
            }, 100);
        });
    </script>
</div>