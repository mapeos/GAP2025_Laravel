@extends('template.base')

@section('content')
<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    <div class="d-flex mb-3">
        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#crearEventoModal">
            Crear nuevo evento
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaModal">
            Solicitar cita/consulta con profesor
        </button>
    </div>

    <!-- Modal para crear nuevo evento -->
    <div class="modal fade" id="crearEventoModal" tabindex="-1" aria-labelledby="crearEventoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearEventoModalLabel">Crear nuevo evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearEvento">
                        <div class="mb-3">
                            <label for="nuevoTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="nuevoTitulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="nuevaDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="nuevaDescripcion"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="nuevaUbicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="nuevaUbicacion">
                        </div>
                        <div class="mb-3">
                            <label for="nuevaUrlVirtual" class="form-label">URL Virtual</label>
                            <input type="url" class="form-control" id="nuevaUrlVirtual">
                        </div>
                        <div class="mb-3">
                            <label for="nuevoTipoEvento" class="form-label">Tipo de evento</label>
                            <select class="form-select" id="nuevoTipoEvento" required>
                                <option value="">Seleccione un tipo</option>
                                @foreach(\App\Models\TipoEvento::where('status', true)->get() as $tipo)
                                    <option value="{{ $tipo->id }}" data-color="{{ $tipo->color }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nuevaFechaInicio" class="form-label">Fecha inicio</label>
                                <input type="datetime-local" class="form-control" id="nuevaFechaInicio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nuevaFechaFin" class="form-label">Fecha fin</label>
                                <input type="datetime-local" class="form-control" id="nuevaFechaFin" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnCrearEvento" class="btn btn-primary">Crear evento</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal existente para solicitud de cita -->
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

    <!-- Modal existente para editar eventos -->
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
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnEliminar" class="btn btn-danger">Eliminar</button>
                    <button type="button" id="btnGuardar" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

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
                editable: true,
                selectable: true, // Permite seleccionar fechas en el calendario
                dayMaxEventRows: 3, // Limita a mostrar máximo 3 eventos por día
                moreLinkText: '...', // Texto para el enlace "más"
                moreLinkClick: 'popover', // Muestra los eventos adicionales en un popover al hacer clic
                select: function(info) {
                    // Cuando el usuario selecciona un rango de fechas en el calendario
                    // Crear un objeto Date a partir de la fecha de inicio
                    let startDate = new Date(info.start);

                    // Formatear la fecha para el campo datetime-local (YYYY-MM-DDThh:mm)
                    // Por defecto establecer la hora a las 8:00 AM
                    startDate.setHours(8, 0, 0);
                    let formattedStartDate = startDate.toISOString().slice(0, 16);
                    document.getElementById('nuevaFechaInicio').value = formattedStartDate;

                    // Si hay una fecha de fin, usarla; de lo contrario, usar la fecha de inicio + 1 hora
                    let endDate;
                    if (info.end) {
                        endDate = new Date(info.end);
                        // Restar un día si es un evento de día completo, ya que FullCalendar incluye el día siguiente
                        if (info.allDay) {
                            endDate.setDate(endDate.getDate() - 1);
                        }
                    } else {
                        // Si no hay fecha de fin, establecer la misma fecha de inicio + 1 hora
                        endDate = new Date(startDate);
                        endDate.setHours(endDate.getHours() + 1);
                    }

                    // Formatear la fecha de fin
                    endDate.setHours(9, 0, 0); // Por defecto establecer la hora a las 9:00 AM
                    let formattedEndDate = endDate.toISOString().slice(0, 16);
                    document.getElementById('nuevaFechaFin').value = formattedEndDate;

                    // Mostrar el modal para crear evento
                    var modal = new bootstrap.Modal(document.getElementById('crearEventoModal'));
                    modal.show();
                },

                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    document.getElementById('eventoId').value = info.event.id;
                    document.getElementById('titulo').value = info.event.title;
                    document.getElementById('descripcion').value = info.event.extendedProps.descripcion || '';
                    var modal = new bootstrap.Modal(document.getElementById('eventoModal'));
                    modal.show();
                },

                eventDrop: function(info) {
                    fetch(`/eventos/${info.event.id}`, {
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
                        info.revert();
                    });
                }
            });
            calendar.render();

            // Código existente para btnEliminar y btnGuardar
            document.getElementById('btnEliminar').onclick = function() {
                // Referencia al botón
                const btnEliminar = document.getElementById('btnEliminar');

                let id = document.getElementById('eventoId').value;
                if (confirm('¿Seguro que deseas eliminar este evento?')) {
                    // Deshabilitar el botón y mostrar indicador de carga
                    btnEliminar.disabled = true;
                    btnEliminar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...';

                    fetch(`/eventos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        // Restaurar el botón
                        btnEliminar.disabled = false;
                        btnEliminar.innerHTML = 'Eliminar';

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
                        // Restaurar el botón en caso de error
                        btnEliminar.disabled = false;
                        btnEliminar.innerHTML = 'Eliminar';

                        console.error('Error:', error);
                        alert('Error al eliminar el evento.');
                    });
                }
            };

            document.getElementById('btnGuardar').onclick = function() {
                // Referencia al botón
                const btnGuardar = document.getElementById('btnGuardar');

                // Deshabilitar el botón y mostrar indicador de carga
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

                let id = document.getElementById('eventoId').value;
                let titulo = document.getElementById('titulo').value;
                let descripcion = document.getElementById('descripcion').value;

                // Validar que el título no esté vacío
                if (!titulo) {
                    // Restaurar el botón si hay error de validación
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = 'Guardar cambios';
                    alert('El título no puede estar vacío.');
                    return;
                }

                fetch(`/eventos/${id}`, {
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
                    // Restaurar el botón
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = 'Guardar cambios';

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
                })
                .catch(error => {
                    // Restaurar el botón en caso de error
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = 'Guardar cambios';

                    console.error('Error:', error);
                    alert('Error al actualizar el evento.');
                });
            };

            // Nuevo código para crear eventos
            document.getElementById('btnCrearEvento').onclick = function() {
                // Referencia al botón
                const btnCrearEvento = document.getElementById('btnCrearEvento');

                // Deshabilitar el botón y mostrar indicador de carga
                btnCrearEvento.disabled = true;
                btnCrearEvento.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creando...';

                let titulo = document.getElementById('nuevoTitulo').value;
                let descripcion = document.getElementById('nuevaDescripcion').value;
                let ubicacion = document.getElementById('nuevaUbicacion').value;
                let url_virtual = document.getElementById('nuevaUrlVirtual').value;
                let tipo_evento_id = document.getElementById('nuevoTipoEvento').value;
                let fecha_inicio = document.getElementById('nuevaFechaInicio').value;
                let fecha_fin = document.getElementById('nuevaFechaFin').value;

                // Validar que los campos requeridos estén completos
                if (!titulo || !tipo_evento_id || !fecha_inicio || !fecha_fin) {
                    // Restaurar el botón si hay error de validación
                    btnCrearEvento.disabled = false;
                    btnCrearEvento.innerHTML = 'Crear evento';
                    alert('Por favor complete todos los campos requeridos.');
                    return;
                }

                // Validar que la fecha de fin sea posterior o igual a la fecha de inicio
                if (new Date(fecha_fin) < new Date(fecha_inicio)) {
                    // Restaurar el botón si hay error de validación
                    btnCrearEvento.disabled = false;
                    btnCrearEvento.innerHTML = 'Crear evento';
                    alert('La fecha de fin debe ser posterior o igual a la fecha de inicio.');
                    return;
                }

                // Enviar solicitud para crear el evento
                fetch('/eventos', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        titulo: titulo,
                        descripcion: descripcion,
                        ubicacion: ubicacion,
                        url_virtual: url_virtual,
                        tipo_evento_id: tipo_evento_id,
                        fecha_inicio: fecha_inicio,
                        fecha_fin: fecha_fin
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al crear el evento.');
                    }
                    return response.json();
                })
                .then(data => {
                    // Restaurar el botón
                    btnCrearEvento.disabled = false;
                    btnCrearEvento.innerHTML = 'Crear evento';

                    if (data.success) {
                        // Cerrar el modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('crearEventoModal'));
                        modal.hide();

                        // Obtener el color del tipo de evento seleccionado
                        let tipoEventoSelect = document.getElementById('nuevoTipoEvento');
                        let selectedOption = tipoEventoSelect.options[tipoEventoSelect.selectedIndex];
                        let color = selectedOption.getAttribute('data-color');

                        // Añadir el evento al calendario
                        calendar.addEvent({
                            id: data.evento.id,
                            title: data.evento.titulo,
                            start: data.evento.fecha_inicio,
                            end: data.evento.fecha_fin,
                            color: color,
                            extendedProps: {
                                descripcion: data.evento.descripcion
                            }
                        });

                        // Limpiar el formulario
                        document.getElementById('formCrearEvento').reset();

                        alert('Evento creado exitosamente.');
                    } else {
                        alert(data.message || 'Error al crear el evento.');
                    }
                })
                .catch(error => {
                    // Restaurar el botón en caso de error
                    btnCrearEvento.disabled = false;
                    btnCrearEvento.innerHTML = 'Crear evento';

                    console.error('Error:', error);
                    alert('Error al crear el evento.');
                });
            };
        });
    </script>
</div>
@endsection
