<script>
// Variables globales
let calendar;
let currentView = 'calendar';
let eventos = @json($eventos ?? []);
let userRole = '{{ Auth::user()->roles->first()->name ?? "" }}';

// Iniciar calendario
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    initializeEventListeners();

    // Actualizar eventos después de la carga inicial
    setTimeout(() => {
        loadEventosAjax();
    }, 500);
});

// Iniciar FullCalendar
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        firstDay: 1, // Lunes = 1, Domingo = 0
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
        },
        dateClick: handleDateClick,
        select: handleDateClick,
        eventDisplay: 'block',
        eventDidMount: function(info) {
            const event = info.event;
            const tooltip = `${event.title}\n${event.extendedProps.descripcion || ''}\n${new Date(event.start).toLocaleString('es-ES')}`;
            info.el.title = tooltip;
        },
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

    // Inicializar modales
    initializeModales();
}

// Cargar eventos vía AJAX
function loadEventosAjax() {
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        calendarEl.classList.add('loading');
    }

    fetch('/facultativo/calendario/citas')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            eventos = data;
            if (calendar) {
                const currentEvents = calendar.getEvents();
                currentEvents.forEach(event => event.remove());
                calendar.addEventSource(eventos);
            }
        })
        .catch(error => {
            console.error('Error cargando eventos:', error);
            showNotification('Error al cargar eventos: ' + error.message, 'error');
        })
        .finally(() => {
            if (calendarEl) {
                calendarEl.classList.remove('loading');
            }
        });
}

// Manejar clic en fecha
function handleDateClick(info) {
    info.jsEvent.preventDefault();

    if (calendar.view.type === 'dayGridMonth') {
        calendar.changeView('timeGridDay', info.dateStr);
        return;
    }

    const modal = document.getElementById('crearEventoModal');
    if (modal) {
        document.getElementById('fecha_inicio').value = info.start.toISOString().slice(0, 16);
        document.getElementById('fecha_fin').value = info.end.toISOString().slice(0, 16);
        new bootstrap.Modal(modal).show();
    }
}

// Manejar clic en evento
function handleEventClick(info) {
    info.jsEvent.preventDefault();
    const event = info.event;

    if (calendar.view.type === 'timeGridDay') {
        const modal = document.getElementById('editarEventoModal');
        if (modal) {
            document.getElementById('editEventoId').value = event.id;
            
            if (userRole === 'Facultativo' || userRole === 'Administrador') {
                // Versión editable para facultativos y administradores
                document.getElementById('editTitulo').value = event.title;
                document.getElementById('editDescripcion').value = event.extendedProps.descripcion || '';
                document.getElementById('editFechaInicio').value = event.start.toISOString().slice(0, 16);
                document.getElementById('editFechaFin').value = event.end.toISOString().slice(0, 16);
                
                // Mostrar campos editables
                document.querySelectorAll('.edit-field').forEach(field => {
                    field.style.display = 'block';
                });
            } else {
                // Versión de solo lectura para pacientes
                document.getElementById('viewTitulo').textContent = event.title;
                document.getElementById('viewDescripcion').textContent = event.extendedProps.descripcion || '';
                document.getElementById('viewFechaInicio').textContent = new Date(event.start).toLocaleString('es-ES');
                document.getElementById('viewFechaFin').textContent = new Date(event.end).toLocaleString('es-ES');
                
                // Ocultar campos editables
                document.querySelectorAll('.edit-field').forEach(field => {
                    field.style.display = 'none';
                });
            }
            
            new bootstrap.Modal(modal).show();
        }
    }
}

// Manejar arrastre de evento
function handleEventDrop(info) {
    const event = info.event;
    const newStart = event.start.toISOString();
    const newEnd = event.end ? event.end.toISOString() : newStart;

    fetch(`/facultativo/calendario/citas/${event.id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            fecha_inicio: newStart,
            fecha_fin: newEnd
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Evento actualizado correctamente', 'success');
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

// Manejar redimensionamiento de evento
function handleEventResize(info) {
    const event = info.event;
    const newStart = event.start.toISOString();
    const newEnd = event.end.toISOString();

    fetch(`/facultativo/calendario/citas/${event.id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            fecha_inicio: newStart,
            fecha_fin: newEnd
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Evento actualizado correctamente', 'success');
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
        btnAgendaView.innerHTML = '<i class="ri-calendar-line me-1"></i> Ver calendario';
        currentView = 'agenda';
        loadAgendaView();
    } else {
        calendarView.classList.remove('d-none');
        agendaView.classList.add('d-none');
        btnAgendaView.innerHTML = '<i class="ri-list-check-line me-1"></i> Ver agenda';
        currentView = 'calendar';
    }
}

// Cargar vista de agenda
function loadAgendaView() {
    const agendaList = document.getElementById('agendaList');
    if (!agendaList) return;

    agendaList.innerHTML = '';

    if (eventos.length === 0) {
        agendaList.innerHTML = '<div class="text-center text-muted py-4">No hay eventos programados</div>';
        return;
    }

    // Ordenar eventos por fecha
    const sortedEventos = eventos.sort((a, b) => new Date(a.start) - new Date(b.start));

    sortedEventos.forEach(evento => {
        const eventDate = new Date(evento.start);
        const eventEnd = new Date(evento.end);
        
        const eventElement = document.createElement('div');
        eventElement.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
        eventElement.innerHTML = `
            <div class="flex-grow-1">
                <h6 class="mb-1">${evento.title}</h6>
                <p class="mb-1 text-muted">${evento.extendedProps.descripcion || ''}</p>
                <small class="text-muted">
                    <i class="ri-time-line me-1"></i>
                    ${eventDate.toLocaleDateString('es-ES')} ${eventDate.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'})} - ${eventEnd.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'})}
                </small>
            </div>
            <div class="ms-3">
                <button class="btn btn-sm btn-outline-primary" onclick="viewEvent('${evento.id}')">
                    <i class="ri-eye-line"></i>
                </button>
            </div>
        `;
        
        agendaList.appendChild(eventElement);
    });
}

// Ver evento desde agenda
function viewEvent(eventId) {
    const event = eventos.find(e => e.id === eventId);
    if (event) {
        handleEventClick({ event: event, jsEvent: { preventDefault: () => {} } });
    }
}

// Inicializar modales
function initializeModales() {
    // Modal de crear evento
    const crearEventoModal = document.getElementById('crearEventoModal');
    if (crearEventoModal) {
        const crearEventoForm = crearEventoModal.querySelector('form');
        if (crearEventoForm) {
            crearEventoForm.addEventListener('submit', handleCrearEvento);
        }
    }

    // Modal de editar evento
    const editarEventoModal = document.getElementById('editarEventoModal');
    if (editarEventoModal) {
        const editarEventoForm = editarEventoModal.querySelector('form');
        if (editarEventoForm) {
            editarEventoForm.addEventListener('submit', handleEditarEvento);
        }
    }

    // Modal de solicitud de cita
    const solicitudCitaModal = document.getElementById('solicitudCitaModal');
    if (solicitudCitaModal) {
        const solicitudCitaForm = solicitudCitaModal.querySelector('form');
        if (solicitudCitaForm) {
            solicitudCitaForm.addEventListener('submit', handleSolicitudCita);
        }
    }

    // Modal de solicitud de cita AI
    const solicitudCitaAiModal = document.getElementById('solicitudCitaAiModal');
    if (solicitudCitaAiModal) {
        const solicitudCitaAiForm = solicitudCitaAiModal.querySelector('form');
        if (solicitudCitaAiForm) {
            solicitudCitaAiForm.addEventListener('submit', handleSolicitudCitaAi);
        }
    }
}

// Manejar crear evento
function handleCrearEvento(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('/facultativo/calendario/citas', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Evento creado correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('crearEventoModal')).hide();
            e.target.reset();
            loadEventosAjax();
        } else {
            showNotification('Error al crear evento: ' + data.message, 'error');
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
    const formData = new FormData(e.target);
    const eventId = document.getElementById('editEventoId').value;
    
    fetch(`/facultativo/calendario/citas/${eventId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Evento actualizado correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editarEventoModal')).hide();
            loadEventosAjax();
        } else {
            showNotification('Error al actualizar evento: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al actualizar evento', 'error');
    });
}

// Manejar solicitud de cita
function handleSolicitudCita(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('/facultativo/solicitud-cita', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Solicitud enviada correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('solicitudCitaModal')).hide();
            e.target.reset();
        } else {
            showNotification('Error al enviar solicitud: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar solicitud', 'error');
    });
}

// Manejar solicitud de cita AI
function handleSolicitudCitaAi(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    fetch('/facultativo/solicitud-cita-ai', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Solicitud AI enviada correctamente', 'success');
            bootstrap.Modal.getInstance(document.getElementById('solicitudCitaAiModal')).hide();
            e.target.reset();
        } else {
            showNotification('Error al enviar solicitud AI: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al enviar solicitud AI', 'error');
    });
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Implementar según tu sistema de notificaciones
    console.log(`${type.toUpperCase()}: ${message}`);
    // Puedes usar toastr, sweetalert2, o cualquier librería de notificaciones
}
</script> 