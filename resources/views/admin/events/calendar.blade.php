@extends('template.base')

@section('content')
<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    <div id="calendar"></div>

    <!-- Modal para editar/eliminar evento -->
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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <!-- Bootstrap JS para el modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: @json($eventos ?? []),
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    // Cargar datos en el modal
                    document.getElementById('eventoId').value = info.event.id;
                    document.getElementById('titulo').value = info.event.title;
                    document.getElementById('descripcion').value = info.event.extendedProps.descripcion || '';
                    var modal = new bootstrap.Modal(document.getElementById('eventoModal'));
                    modal.show();
                }
            });
            calendar.render();

            // Eliminar evento
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
                            calendar.getEventById(id).remove();
                            alert('Evento eliminado exitosamente.');
                        } else {
                            alert('Error al eliminar el evento.');
                        }
                    });
                }
            };

            // Editar evento
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
                        let event = calendar.getEventById(id);
                        event.setProp('title', titulo);
                        event.setExtendedProp('descripcion', descripcion);
                        alert('Evento actualizado exitosamente.');
                    } else {
                        alert('Error al actualizar el evento.');
                    }
                });
            };
        });
    </script>
</div>
@endsection
