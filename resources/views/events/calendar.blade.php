@extends('template.base')

@section('content')
<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#solicitudCitaModal">
        Solicitar cita/consulta con profesor
    </button>

    <div class="modal fade" id="solicitudCitaModal" tabindex="-1" aria-labelledby="solicitudCitaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('solicitud-cita.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="solicitudCitaModalLabel">Solicitar cita/consulta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="profesor_id" class="form-label">Profesor</label>
            <select class="form-select" name="profesor_id" required>
              <option value="">Seleccione un profesor</option>
              @foreach($profesores as $profesor)
                <option value="{{ $profesor->id }}">{{ $profesor->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="motivo" class="form-label">Motivo</label>
            <input type="text" class="form-control" name="motivo" required>
          </div>
          <div class="mb-3">
            <label for="fecha_propuesta" class="form-label">Fecha y hora propuesta</label>
            <input type="datetime-local" class="form-control" name="fecha_propuesta" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enviar solicitud</button>
        </div>
      </div>
    </form>
  </div>
</div>

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
                editable: true, // Habilita drag & drop

                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    // Cargar datos en el modal
                    document.getElementById('eventoId').value = info.event.id;
                    document.getElementById('titulo').value = info.event.title;
                    document.getElementById('descripcion').value = info.event.extendedProps.descripcion || '';
                    var modal = new bootstrap.Modal(document.getElementById('eventoModal'));
                    modal.show();
                },

                eventDrop: function(info) {
                    // Cuando se arrastra y suelta un evento
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
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al actualizar la fecha del evento.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error al actualizar la fecha del evento');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar la fecha del evento.');
                        info.revert(); // Revierte el cambio en el calendario
                    });
                }
            });
            calendar.render();

            document.getElementById('btnEliminar').onclick = function() {
                let id = document.getElementById('eventoId').value;
                if (confirm('¿Seguro que deseas eliminar este evento?')) {
                    fetch(`/admin/eventos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            var modal = bootstrap.Modal.getInstance(document.getElementById('eventoModal'));
                            modal.hide();
                            calendar.getEventById(id).remove();
                            alert('Evento eliminado exitosamente.');
                        } else {
                            response.json().then(data => {
                                alert(data.message || 'Error al eliminar el evento.');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el evento.');
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
