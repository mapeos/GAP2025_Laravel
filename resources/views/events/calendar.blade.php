@extends('template.base')

@section('content')
<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    <div class="d-flex gap-2 mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaModal">
            Solicitar cita/consulta con profesor
        </button>
        @if(Auth::user()->hasRole('profesor'))
            <a href="{{ route('solicitud-cita.recibidas') }}" class="btn btn-info">
                <i class="ri-mail-line"></i> Ver solicitudes recibidas
            </a>
        @else
            <a href="{{ route('solicitud-cita.index') }}" class="btn btn-info">
                <i class="ri-mail-line"></i> Ver mis solicitudes
            </a>
        @endif
    </div>

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
                selectable: true,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    // Aquí puedes agregar la lógica para mostrar detalles del evento si lo necesitas
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
        });
    </script>
</div>
@endsection
