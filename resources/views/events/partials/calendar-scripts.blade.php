<script>
// Variables globales
let calendar;
let currentView = 'calendar';
let eventos = @json($eventos ?? []);

// Inicialización del calendario
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    initializeEventListeners();

    // Opcional: Actualizar eventos después de la carga inicial
    // para asegurar que tenemos los datos más recientes
    setTimeout(() => {
        loadEventosAjax();
    }, 500);
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
        },
        dateClick: handleDateClick,
        eventDisplay: 'block',
        eventDidMount: function(info) {
            const event = info.event;
            const tooltip = `${event.title}\n${event.extendedProps.descripcion || ''}\n${new Date(event.start).toLocaleString('es-ES')}`;
            info.el.title = tooltip;
        },
        viewDidMount: function(info) {
            if (info.view.type === 'timeGridDay') {
                addBackToMonthButton();
            } else {
                removeBackToMonthButton();
            }
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
    // Inicializar modales y sus formularios
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
}

// Cargar eventos vía AJAX
function loadEventosAjax() {
    // Mostrar indicador de carga si es necesario
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        calendarEl.classList.add('loading');
    }

    fetch('/eventos/json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            eventos = data;
            if (calendar) {
                // Refrescar eventos de manera más eficiente
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
            // Quitar indicador de carga
            if (calendarEl) {
                calendarEl.classList.remove('loading');
            }
        });
}

// Manejar clic en fecha (día)
function handleDateClick(info) {
    // Cambiar a vista de día cuando se hace clic en una fecha
    calendar.changeView('timeGridDay', info.dateStr);
}

// Manejar clic en evento
function handleEventClick(info) {
    // Prevenir la navegación predeterminada
    info.jsEvent.preventDefault();

    const event = info.event;

    // Solo abrir modal si estamos en vista de día
    if (calendar.view.type === 'timeGridDay') {
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

            // Seleccionar el tipo de evento correcto
            if (event.extendedProps.tipo_evento_id && document.getElementById('editTipoEventoId')) {
                document.getElementById('editTipoEventoId').value = event.extendedProps.tipo_evento_id;
            }

            // Establecer el estado correcto si existe
            if (event.extendedProps.status !== undefined && document.getElementById('editStatus')) {
                document.getElementById('editStatus').value = event.extendedProps.status ? '1' : '0';
            }

            // Cargar participantes si existen
            if (event.extendedProps.participantes && document.getElementById('editParticipantes')) {
                const participantesSelect = document.getElementById('editParticipantes');
                // Deseleccionar todos los participantes primero
                Array.from(participantesSelect.options).forEach(option => option.selected = false);

                // Seleccionar los participantes del evento
                event.extendedProps.participantes.forEach(participante => {
                    const option = Array.from(participantesSelect.options).find(opt => opt.value == participante.id);
                    if (option) option.selected = true;
                });
            }

            new bootstrap.Modal(modal).show();
        }
    } else {
        // Si estamos en vista de mes, cambiar a vista de día
        calendar.changeView('timeGridDay', event.start);
    }

    // Importante: devolver false para evitar comportamiento predeterminado
    return false;
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

// Actualizar evento vía AJAX (versión mejorada)
function updateEventoAjax(eventoId, data) {
    // Crear un FormData para enviar los datos
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    return fetch(`/eventos/${eventoId}`, {
        method: 'POST', // Cambiamos a POST para mayor compatibilidad
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        // Intentar procesar como JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Si no es JSON, devolver un objeto estándar
            return {
                success: true,
                message: 'Evento actualizado exitosamente'
            };
        }
    });
}

// Manejar editar evento (versión mejorada)
function handleEditarEvento(e) {
    e.preventDefault();
    const eventoId = document.getElementById('editEventoId').value;
    const form = e.target;
    const formData = new FormData(form);

    // Añadir el método PUT para Laravel
    formData.append('_method', 'PUT');

    // Eliminar el campo id del FormData ya que está en la URL
    formData.delete('id');

    const modal = document.getElementById('editarEventoModal');

    // Desactivar todos los elementos del modal
    const allInputs = modal.querySelectorAll('input, select, textarea, button');
    allInputs.forEach(el => el.disabled = true);

    // Desactivar el botón de cierre del modal
    const closeButton = modal.querySelector('.btn-close');
    if (closeButton) closeButton.disabled = true;

    // Obtener el botón de envío y añadir spinner
    const submitButton = form.querySelector('button[type="submit"]');
    let spinner = null;
    let btnText = null;

    if (submitButton) {
        btnText = submitButton.querySelector('.btn-text') || submitButton;
        const originalText = btnText.textContent;
        btnText.textContent = 'Actualizando...';

        // Crear y añadir spinner si no existe
        spinner = submitButton.querySelector('.spinner-border');
        if (!spinner) {
            spinner = document.createElement('span');
            spinner.className = 'spinner-border spinner-border-sm ms-2';
            spinner.setAttribute('role', 'status');
            spinner.setAttribute('aria-hidden', 'true');
            submitButton.appendChild(spinner);
        } else {
            spinner.classList.remove('d-none');
        }
    }

    fetch(`/eventos/${eventoId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        // Intentar procesar como JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Si no es JSON, devolver un objeto estándar
            return {
                success: true,
                message: 'Evento actualizado exitosamente'
            };
        }
    })
    .then(result => {
        if (result.success) {
            showNotification('Evento actualizado exitosamente', 'success');
            try {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            } catch (error) {
                console.error('Error al cerrar el modal:', error);
                // Fallback para cerrar el modal
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
            loadEventosAjax();
        } else {
            showNotification(result.message || 'Error al actualizar evento', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(`Error al actualizar evento: ${error.message}`, 'error');
    })
    .finally(() => {
        // Re-habilitar todos los elementos del modal
        allInputs.forEach(el => el.disabled = false);
        if (closeButton) closeButton.disabled = false;

        // Re-habilitar el botón de envío y ocultar spinner
        if (submitButton) {
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.textContent = 'Guardar Cambios';
        }
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
function showNotification(message, type = 'info', autoClose = true) {
    // Implementar sistema de notificaciones (Toastr, SweetAlert, etc.)
    console.log(`${type.toUpperCase()}: ${message}`);

    // Ejemplo con SweetAlert2 (si está disponible)
    if (typeof Swal !== 'undefined') {
        const options = {
            title: '',
            text: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: autoClose ? 3000 : null,
            timerProgressBar: autoClose,
        };

        return Swal.fire(options);
    } else {
        // Fallback a alert básico
        if (type === 'error') {
            alert(`Error: ${message}`);
        } else {
            alert(message);
        }
        return null;
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

// Agregar botón para volver a vista de mes
function addBackToMonthButton() {
    // Remover botón existente si hay
    removeBackToMonthButton();

    const toolbar = document.querySelector('.fc-toolbar');
    if (toolbar) {
        const backButton = document.createElement('button');
        backButton.className = 'btn btn-secondary btn-sm ms-2';
        backButton.innerHTML = '<i class="ri-calendar-line"></i> Ver mes';
        backButton.onclick = function() {
            calendar.changeView('dayGridMonth');
        };

        const rightSection = toolbar.querySelector('.fc-toolbar-chunk:last-child');
        if (rightSection) {
            rightSection.appendChild(backButton);
        }
    }
}

// Remover botón de volver a mes
function removeBackToMonthButton() {
    const backButton = document.querySelector('.fc-toolbar .btn-secondary');
    if (backButton && backButton.innerHTML.includes('Ver mes')) {
        backButton.remove();
    }
}

// Función para alternar entre vista de calendario y agenda
function toggleAgendaView() {
    const calendarContainer = document.getElementById('calendar');
    const agendaContainer = document.getElementById('agendaView');

    if (currentView === 'calendar') {
        // Cambiar a vista de agenda
        calendarContainer.classList.add('d-none');
        agendaContainer.classList.remove('d-none');
        currentView = 'agenda';

        // Cargar eventos en la vista de agenda
        renderAgendaView();

        // Cambiar texto del botón
        const btnAgendaView = document.getElementById('btnAgendaView');
        if (btnAgendaView) {
            btnAgendaView.innerHTML = '<i class="ri-calendar-line me-1"></i> Ver calendario';
        }
    } else {
        // Cambiar a vista de calendario
        calendarContainer.classList.remove('d-none');
        agendaContainer.classList.add('d-none');
        currentView = 'calendar';

        // Cambiar texto del botón
        const btnAgendaView = document.getElementById('btnAgendaView');
        if (btnAgendaView) {
            btnAgendaView.innerHTML = '<i class="ri-list-check-line me-1"></i> Ver agenda';
        }
    }
}

// Función para renderizar eventos en la vista de agenda
function renderAgendaView() {
    const agendaList = document.getElementById('agendaList');
    if (!agendaList) return;

    // Limpiar lista actual
    agendaList.innerHTML = '';

    // Ordenar eventos por fecha
    const sortedEventos = [...eventos].sort((a, b) => {
        return new Date(a.start || a.fecha_inicio) - new Date(b.start || b.fecha_inicio);
    });

    // Agrupar eventos por fecha
    const eventsByDate = {};
    sortedEventos.forEach(evento => {
        const eventDate = new Date(evento.start || evento.fecha_inicio);
        const dateKey = eventDate.toISOString().split('T')[0];

        if (!eventsByDate[dateKey]) {
            eventsByDate[dateKey] = [];
        }
        eventsByDate[dateKey].push(evento);
    });

    // Crear elementos para cada fecha y sus eventos
    Object.keys(eventsByDate).sort().forEach(dateKey => {
        const date = new Date(dateKey);
        const formattedDate = date.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Crear encabezado de fecha
        const dateHeader = document.createElement('div');
        dateHeader.className = 'list-group-item list-group-item-secondary';
        dateHeader.innerHTML = `<strong>${formattedDate}</strong>`;
        agendaList.appendChild(dateHeader);

        // Agregar eventos de esta fecha
        eventsByDate[dateKey].forEach(evento => {
            const eventItem = document.createElement('div');
            eventItem.className = 'list-group-item list-group-item-action';

            const eventTime = new Date(evento.start || evento.fecha_inicio).toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const endTime = evento.end || evento.fecha_fin ? new Date(evento.end || evento.fecha_fin).toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            }) : '';

            const timeDisplay = endTime ? `${eventTime} - ${endTime}` : eventTime;

            eventItem.innerHTML = `
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">${evento.title || evento.titulo}</h5>
                    <small>${timeDisplay}</small>
                </div>
                <p class="mb-1">${evento.extendedProps?.descripcion || evento.descripcion || ''}</p>
                <small>
                    ${evento.extendedProps?.ubicacion || evento.ubicacion ? `<i class="ri-map-pin-line"></i> ${evento.extendedProps?.ubicacion || evento.ubicacion}` : ''}
                    ${evento.extendedProps?.url_virtual || evento.url_virtual ? `<i class="ri-global-line ms-2"></i> <a href="${evento.extendedProps?.url_virtual || evento.url_virtual}" target="_blank">Enlace virtual</a>` : ''}
                </small>
            `;

            // Agregar evento click para ver detalles
            eventItem.addEventListener('click', function() {
                // Puedes implementar aquí la lógica para mostrar detalles del evento
                // Por ejemplo, abrir el modal de edición o una página de detalles
                const eventId = evento.id;
                if (eventId) {
                    window.location.href = `/eventos/${eventId}`;
                }
            });

            agendaList.appendChild(eventItem);
        });
    });

    // Mensaje si no hay eventos
    if (Object.keys(eventsByDate).length === 0) {
        const noEvents = document.createElement('div');
        noEvents.className = 'list-group-item text-center text-muted';
        noEvents.innerHTML = '<i class="ri-calendar-line fs-1 mb-2"></i><p>No hay eventos programados</p>';
        agendaList.appendChild(noEvents);
    }
}
</script>
