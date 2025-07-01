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
    initializeModals();
}

// Cargar eventos vía AJAX
function loadEventosAjax() {
    // Mostrar indicador de carga si es necesario
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        calendarEl.classList.add('loading'); // Puedes añadir un estilo CSS para esto
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

            // Verificar si extendedProps existe antes de acceder a descripcion
            const descripcion = evento.extendedProps && evento.extendedProps.descripcion ? evento.extendedProps.descripcion : '';

            eventoDiv.innerHTML = `
                <div class="ms-2 me-auto">
                    <div class="fw-bold">${evento.title}</div>
                    <small class="text-muted">${descripcion}</small>
                </div>
                <span class="badge bg-primary rounded-pill" style="background-color: ${evento.color || '#007bff'} !important;">
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

    // Deshabilitar el botón de envío para evitar múltiples envíos
    const submitButton = e.target.querySelector('button[type="submit"]');
    const spinner = document.getElementById('crearEventoSpinner');
    const btnText = document.getElementById('crearEventoBtnText');

    if (submitButton) {
        submitButton.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
        if (btnText) btnText.textContent = 'Creando...';
    }

    fetch('/eventos', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'  // Añadir este encabezado para indicar que esperamos JSON
        },
        body: formData
    })
    .then(response => {
        // Verificar si la respuesta es correcta antes de intentar procesarla como JSON
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        // Verificar el tipo de contenido para asegurarse de que es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('La respuesta no es JSON válido. Tipo de contenido: ' + contentType);
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            showNotification('Evento creado exitosamente', 'success');
            // Usar try-catch para manejar posibles errores al cerrar el modal
            try {
                const modalElement = document.getElementById('crearEventoModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                    // Asegurar que el backdrop se elimine correctamente después de que el modal se oculte
                    setTimeout(() => {
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                        // Forzar el reflow del DOM para que los cambios se apliquen
                        document.body.offsetHeight;
                        // Establecer explícitamente el overflow después del reflow
                        document.body.style.overflow = 'visible';
                        // Asegurar que la barra de scroll sea visible
                        document.documentElement.style.overflow = 'auto';
                        document.documentElement.style.overflowY = 'scroll';
                    }, 300); // Esperar a que termine la animación de cierre
                } else {
                    // Si no hay instancia, intentar cerrar de otra manera
                    $(modalElement).modal('hide'); // Alternativa con jQuery si está disponible
                    // Asegurar limpieza después de jQuery
                    setTimeout(() => {
                        // Y eliminar el backdrop si existe
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                        // Forzar el reflow del DOM para que los cambios se apliquen
                        document.body.offsetHeight;
                        // Establecer explícitamente el overflow después del reflow
                        document.body.style.overflow = 'visible';
                        // Asegurar que la barra de scroll sea visible
                        document.documentElement.style.overflow = 'auto';
                        document.documentElement.style.overflowY = 'scroll';
                    }, 300);
                }
            } catch (error) {
                console.error('Error al cerrar el modal:', error);
                // Intentar cerrar el modal de otra manera
                const modalElement = document.getElementById('crearEventoModal');
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                // Forzar el reflow del DOM para que los cambios se apliquen
                document.body.offsetHeight;
                // Establecer explícitamente el overflow después del reflow
                document.body.style.overflow = 'visible';
                // Asegurar que la barra de scroll sea visible
                document.documentElement.style.overflow = 'auto';
                document.documentElement.style.overflowY = 'scroll';
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
            e.target.reset();
            loadEventosAjax();
        } else {
            showNotification(result.message || 'Error al crear evento', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al crear evento: ' + error.message, 'error');
    })
    .finally(() => {
        // Re-habilitar el botón de envío y ocultar spinner
        if (submitButton) {
            submitButton.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.textContent = 'Crear Evento';
        }
        // Asegurar que el scroll esté habilitado
        document.body.style.overflow = 'auto';
    });
}

// Manejar editar evento
function handleEditarEvento(e) {
    e.preventDefault();
    const eventoId = document.getElementById('editEventoId').value;
    const formData = new FormData(e.target);
    const modal = document.getElementById('editarEventoModal');

    // Obtener el botón de envío y añadir spinner
    const submitButton = e.target.querySelector('button[type="submit"]');
    let spinner = null;
    let btnText = null;

    // Desactivar todos los elementos del modal
    const allInputs = modal.querySelectorAll('input, select, textarea, button');
    allInputs.forEach(el => el.disabled = true);
    
    // Desactivar el botón de cierre del modal
    const closeButton = modal.querySelector('.btn-close');
    if (closeButton) closeButton.disabled = true;

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

    fetch(`/admin/events/${eventoId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Si no es JSON, convertir a un objeto JSON simulado
            return {
                success: response.ok,
                message: response.ok ? 'Evento actualizado exitosamente' : 'Error al actualizar evento'
            };
        }
    })
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
</script>
