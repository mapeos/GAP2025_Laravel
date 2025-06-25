<script>
// Variables globales
let calendar;
let currentView = 'calendar';
let eventos = @json($eventos ?? []);

// Inicialización del calendario
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    initializeEventListeners();
    loadEventosAjax();
});

// Inicializar FullCalendar
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista'
        },
        events: eventos,
        eventClick: handleEventClick,
        eventDrop: handleEventDrop,
        eventResize: handleEventResize,
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        height: 'auto',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });

    calendar.render();
}

// Inicializar event listeners
function initializeEventListeners() {
    // Botón de vista agenda
    const btnAgendaView = document.getElementById('btnAgendaView');
    if (btnAgendaView) {
        btnAgendaView.addEventListener('click', toggleAgendaView);
    }

    // Botón flotante
    const fabCrearEvento = document.getElementById('fabCrearEvento');
    if (fabCrearEvento) {
        fabCrearEvento.addEventListener('click', () => {
            const modal = document.getElementById('crearEventoModal');
            if (modal) {
                new bootstrap.Modal(modal).show();
            }
        });
    }

    // Modales
    initializeModals();
}

// Cargar eventos vía AJAX
function loadEventosAjax() {
    fetch('/events/json')
        .then(response => response.json())
        .then(data => {
            eventos = data;
            if (calendar) {
                calendar.removeAllEvents();
                calendar.addEventSource(eventos);
            }
        })
        .catch(error => console.error('Error cargando eventos:', error));
}

// Manejar clic en evento
function handleEventClick(info) {
    const event = info.event;
    const modal = document.getElementById('editarEventoModal');
    
    if (modal) {
        // Llenar el modal con los datos del evento
        document.getElementById('editEventoId').value = event.id;
        document.getElementById('editTitulo').value = event.title;
        document.getElementById('editDescripcion').value = event.extendedProps.descripcion || '';
        document.getElementById('editFechaInicio').value = event.start.toISOString().slice(0, 16);
        document.getElementById('editFechaFin').value = event.end ? event.end.toISOString().slice(0, 16) : '';
        document.getElementById('editUbicacion').value = event.extendedProps.ubicacion || '';
        document.getElementById('editUrlVirtual').value = event.extendedProps.url_virtual || '';
        
        new bootstrap.Modal(modal).show();
    }
}

// Manejar arrastre de evento
function handleEventDrop(info) {
    const event = info.event;
    updateEventoAjax(event.id, {
        fecha_inicio: event.start.toISOString(),
        fecha_fin: event.end ? event.end.toISOString() : event.start.toISOString()
    });
}

// Manejar redimensionamiento de evento
function handleEventResize(info) {
    const event = info.event;
    updateEventoAjax(event.id, {
        fecha_inicio: event.start.toISOString(),
        fecha_fin: event.end.toISOString()
    });
}

// Actualizar evento vía AJAX
function updateEventoAjax(eventoId, data) {
    fetch(`/admin/events/${eventoId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Evento actualizado exitosamente', 'success');
        } else {
            showNotification('Error al actualizar evento', 'error');
            calendar.refetchEvents();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al actualizar evento', 'error');
        calendar.refetchEvents();
    });
}

// Alternar vista de agenda
function toggleAgendaView() {
    const calendarView = document.getElementById('calendar');
    const agendaView = document.getElementById('agendaView');
    const btnAgendaView = document.getElementById('btnAgendaView');

    if (currentView === 'calendar') {
        calendarView.classList.add('d-none');
        agendaView.classList.remove('d-none');
        btnAgendaView.innerHTML = '<i class="ri-calendar-line"></i> Ver calendario';
        currentView = 'agenda';
        renderAgendaView();
    } else {
        calendarView.classList.remove('d-none');
        agendaView.classList.add('d-none');
        btnAgendaView.innerHTML = '<i class="ri-list-check-line"></i> Ver agenda';
        currentView = 'calendar';
    }
}

// Renderizar vista de agenda
function renderAgendaView() {
    const agendaList = document.getElementById('agendaList');
    if (!agendaList) return;

    agendaList.innerHTML = '';
    
    // Agrupar eventos por fecha
    const eventosPorFecha = {};
    eventos.forEach(evento => {
        const fecha = new Date(evento.start).toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        if (!eventosPorFecha[fecha]) {
            eventosPorFecha[fecha] = [];
        }
        eventosPorFecha[fecha].push(evento);
    });

    // Renderizar eventos agrupados
    Object.keys(eventosPorFecha).sort().forEach(fecha => {
        const fechaDiv = document.createElement('div');
        fechaDiv.className = 'list-group-item list-group-item-secondary fw-bold';
        fechaDiv.textContent = fecha.charAt(0).toUpperCase() + fecha.slice(1);
        agendaList.appendChild(fechaDiv);

        eventosPorFecha[fecha].forEach(evento => {
            const eventoDiv = document.createElement('div');
            eventoDiv.className = 'list-group-item d-flex justify-content-between align-items-start';
            eventoDiv.innerHTML = `
                <div class="ms-2 me-auto">
                    <div class="fw-bold">${evento.title}</div>
                    <small class="text-muted">${evento.extendedProps.descripcion || ''}</small>
                </div>
                <span class="badge bg-primary rounded-pill" style="background-color: ${evento.color} !important;">
                    ${new Date(evento.start).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}
                </span>
            `;
            eventoDiv.addEventListener('click', () => handleEventClick({event: evento}));
            agendaList.appendChild(eventoDiv);
        });
    });
}

// Inicializar modales
function initializeModals() {
    // Modal crear evento
    const crearEventoForm = document.getElementById('crearEventoForm');
    if (crearEventoForm) {
        crearEventoForm.addEventListener('submit', handleCrearEvento);
    }

    // Modal editar evento
    const editarEventoForm = document.getElementById('editarEventoForm');
    if (editarEventoForm) {
        editarEventoForm.addEventListener('submit', handleEditarEvento);
    }

    // Modal solicitud cita
    const solicitudCitaForm = document.getElementById('solicitudCitaForm');
    if (solicitudCitaForm) {
        solicitudCitaForm.addEventListener('submit', handleSolicitudCita);
    }

    // Modal solicitud cita AI
    const solicitudCitaAiForm = document.getElementById('solicitudCitaAiForm');
    if (solicitudCitaAiForm) {
        solicitudCitaAiForm.addEventListener('submit', handleSolicitudCitaAi);
    }
}

// Manejar crear evento
function handleCrearEvento(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('/eventos', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Evento creado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('crearEventoModal')).hide();
            e.target.reset();
            loadEventosAjax();
        } else {
            showNotification(result.message || 'Error al crear evento', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al crear evento', 'error');
    });
}

// Manejar editar evento
function handleEditarEvento(e) {
    e.preventDefault();
    const eventoId = document.getElementById('editEventoId').value;
    const formData = new FormData(e.target);
    
    fetch(`/admin/events/${eventoId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Evento actualizado exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editarEventoModal')).hide();
            loadEventosAjax();
        } else {
            showNotification(result.message || 'Error al actualizar evento', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al actualizar evento', 'error');
    });
}

// Manejar solicitud cita
function handleSolicitudCita(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('/solicitud-cita', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Solicitud enviada exitosamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('solicitudCitaModal')).hide();
            e.target.reset();
        } else {
            showNotification(result.message || 'Error al enviar solicitud', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar solicitud', 'error');
    });
}

// Manejar solicitud cita AI
function handleSolicitudCitaAi(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('/ai/appointments/suggest', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Sugerencia de cita generada exitosamente', 'success');
            // Aquí podrías mostrar las sugerencias en un modal
        } else {
            showNotification(result.message || 'Error al generar sugerencia', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al generar sugerencia', 'error');
    });
}

// Mostrar notificación
function showNotification(message, type = 'info') {
    // Implementar sistema de notificaciones (Toastr, SweetAlert, etc.)
    console.log(`${type.toUpperCase()}: ${message}`);
    
    // Ejemplo básico con alert
    if (type === 'error') {
        alert(`Error: ${message}`);
    } else {
        alert(message);
    }
}

// Inicializar Flatpickr en modales
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar datepickers
    const dateInputs = document.querySelectorAll('input[type="datetime-local"]');
    dateInputs.forEach(input => {
        flatpickr(input, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            locale: "es",
            time_24hr: true
        });
    });
});
</script> 