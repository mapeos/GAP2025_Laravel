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

    // Actualizar eventos después de la carga inicial para asegurar que tenemos los datos más recientes (Opcional)
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
    //prevenimos el comportamiento default
    info.jsEvent.preventDefault();

    // si estamos en la vista cambiamos a la vista dia al hacer click en una fecha.
    if (calendar.view.type === 'dayGridMonth') {
        calendar.changeView ('timeGridDay', info.dateStr);
        return;
    }

    //crear eventos al hacer click en los espacios de hora
    const modal = document.getElementById('crearEventoModal');
    if (modal) {
        document.getElementById('fecha_inicio').value = info.start.toISOString().slice(0, 16);
        document.getElementById('fecha_fin').value = info.end.toISOString().slice(0, 16);
        new bootstrap.Modal(modal).show();
    }

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

            if (userRole === 'Profesor' || userRole === 'Administrador') {
                // Versión editable para profesores y administradores
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
            } else {
                // Versión de solo lectura para estudiantes
                document.getElementById('viewTitulo').textContent = event.title;
                document.getElementById('viewDescripcion').textContent = event.extendedProps.descripcion || 'No disponible';

                // Formatear fechas para mejor legibilidad
                const fechaInicio = new Date(event.start);
                document.getElementById('viewFechaInicio').textContent = fechaInicio.toLocaleString('es-ES');

                if (event.end) {
                    const fechaFin = new Date(event.end);
                    document.getElementById('viewFechaFin').textContent = fechaFin.toLocaleString('es-ES');
                } else {
                    document.getElementById('viewFechaFin').textContent = 'No disponible';
                }

                document.getElementById('viewUbicacion').textContent = event.extendedProps.ubicacion || 'No disponible';

                // Para la URL virtual, crear un enlace si existe
                if (event.extendedProps.url_virtual) {
                    const urlLink = document.createElement('a');
                    urlLink.href = event.extendedProps.url_virtual;
                    urlLink.textContent = event.extendedProps.url_virtual;
                    urlLink.target = '_blank';
                    document.getElementById('viewUrlVirtual').innerHTML = '';
                    document.getElementById('viewUrlVirtual').appendChild(urlLink);
                } else {
                    document.getElementById('viewUrlVirtual').textContent = 'No disponible';
                }

                // Mostrar tipo de evento
                if (event.extendedProps.tipo_evento_id) {
                    // Buscar el nombre del tipo de evento
                    const tipoEventoSelect = document.getElementById('editTipoEventoId');
                    if (tipoEventoSelect) {
                        const option = Array.from(tipoEventoSelect.options).find(opt => opt.value == event.extendedProps.tipo_evento_id);
                        if (option) {
                            document.getElementById('viewTipoEvento').textContent = option.textContent;
                        } else {
                            document.getElementById('viewTipoEvento').textContent = 'Tipo desconocido';
                        }
                    } else {
                        document.getElementById('viewTipoEvento').textContent = 'Tipo desconocido';
                    }
                } else {
                    document.getElementById('viewTipoEvento').textContent = 'No disponible';
                }

                // Mostrar estado
                document.getElementById('viewStatus').textContent = event.extendedProps.status ? 'Activo' : 'Inactivo';

                // Mostrar participantes
                if (event.extendedProps.participantes && event.extendedProps.participantes.length > 0) {
                    const participantesTexto = event.extendedProps.participantes.map(p => p.name).join(', ');
                    document.getElementById('viewParticipantes').textContent = participantesTexto;
                } else {
                    document.getElementById('viewParticipantes').textContent = 'No hay participantes';
                }
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
        if (!confirm('¿Estás seguro de que quieres mover de fecha el evento?')) {
        return; // El usuario canceló la operación
    }

    updateEventoAjax(event.id, {
        fecha_inicio: event.start.toISOString(),
        fecha_fin: event.end ? event.end.toISOString() : event.start.toISOString()
    })
    .then(result => {
        if (!result.success) {
            info.revert();
            showNotification(result.message || 'Error al actualizar evento', 'error');
        }
    })
    .catch(error => {
        info.revert();
        showNotification(`Error al actualizar evento: ${error.message}`, 'error');
    });
}

// Manejar el estirar los eventos
function handleEventResize(info) {
    const event = info.event;
        if (!confirm('¿Estás seguro de que quieres modificar las horas del evento?')) {
        return; // El usuario canceló la operación
    }

    updateEventoAjax(event.id, {
        fecha_inicio: event.start.toISOString(),
        fecha_fin: event.end.toISOString()
    })
    .then(result => {
        if (!result.success) {
            info.revert();
            showNotification(result.message || 'Error al actualizar evento', 'error');
        }
    })
    .catch(error => {
        info.revert();
        showNotification(`Error al actualizar evento: ${error.message}`, 'error');
    });
}

// Actualizacion eventos AJAX
function updateEventoAjax(eventoId, data) {
    // Crear un FormData para enviar los datos
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    formData.append('_method', 'PUT');

    return fetch(`/eventos/${eventoId}`, {
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
    });
}



// Modales

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

//Crear Eventos

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

// Editar eventos

// Manejar editar evento
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

    // Mensaje si no hay eventos
    if (Object.keys(eventsByDate).length === 0) {
        const noEvents = document.createElement('div');
        noEvents.className = 'list-group-item text-center text-muted';
        noEvents.innerHTML = '<i class="ri-calendar-line fs-1 mb-2"></i><p>No hay eventos programados</p>';
        agendaList.appendChild(noEvents);
        return;
    }

    // elementos para cada fecha y sus eventos
    Object.keys(eventsByDate).sort().forEach(dateKey => {
        const date = new Date(dateKey);
        const formattedDate = date.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // contenedor para el día
        const dayContainer = document.createElement('div');
        dayContainer.className = 'day-container';
        agendaList.appendChild(dayContainer);

        // encabezado de fecha
        const dateHeader = document.createElement('div');
        dateHeader.className = 'list-group-item list-group-item-secondary day-header d-flex justify-content-between align-items-center';
        dateHeader.innerHTML = `
            <strong>${formattedDate}</strong>
            <i class="ri-arrow-down-s-line toggle-icon"></i>
        `;
        dayContainer.appendChild(dateHeader);

        // contenedor para los eventos del día
        const eventsContainer = document.createElement('div');
        eventsContainer.className = 'day-events collapsed'; // Agregar 'collapsed' aquí para que esté colapsado por defecto
        dayContainer.appendChild(eventsContainer);

        // Agregar eventos de esta fecha al contenedor
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

            // click para ver detalles
            eventItem.addEventListener('click', function() {
                const eventId = evento.id;
                if (eventId) {
                    window.location.href = `/eventos/${eventId}`;
                }
            });

            eventsContainer.appendChild(eventItem);
        });

        // funcionalidad para expandir/colapsar
        dateHeader.addEventListener('click', function() {
            eventsContainer.classList.toggle('collapsed');
            const icon = dateHeader.querySelector('.toggle-icon');
            if (icon) {
                icon.style.transform = eventsContainer.classList.contains('collapsed') ? 'rotate(-90deg)' : 'rotate(0)';
            }
        });

        // Establecer la rotación inicial del icono (colapsado por defecto)
        const icon = dateHeader.querySelector('.toggle-icon');
        if (icon) {
            icon.style.transform = 'rotate(-90deg)';
        }
    });
}

function deleteEvento(event) {
    // Si se proporciona un evento, prevenir el comportamiento predeterminado
    if (event) {
        event.preventDefault();
    }

    const eventoId = document.getElementById('editEventoId').value;
    if (!confirm('¿Estás seguro de que quieres eliminar este evento?')) {
        return; // El usuario canceló la operación
    }

    const modal = document.getElementById('editarEventoModal');

    // Desactivar todos los elementos del modal
    const allInputs = modal.querySelectorAll('input, select, textarea, button');
    allInputs.forEach(el => el.disabled = true);

    // Desactivar el botón de cierre del modal
    const closeButton = modal.querySelector('.btn-close');
    if (closeButton) closeButton.disabled = true;

    // Obtener el botón de eliminar y añadir spinner
    const deleteButton = document.querySelector('.btn-danger[onclick="deleteEvento(event)"]');
    let spinner = null;
    let btnText = null;

    if (deleteButton) {
        btnText = deleteButton.querySelector('.btn-text') || deleteButton;
        const originalText = btnText.textContent;
        btnText.textContent = 'Eliminando...';

        // Crear y añadir spinner si no existe
        spinner = deleteButton.querySelector('.spinner-border');
        if (!spinner) {
            spinner = document.createElement('span');
            spinner.className = 'spinner-border spinner-border-sm ms-2';
            spinner.setAttribute('role', 'status');
            spinner.setAttribute('aria-hidden', 'true');
            deleteButton.appendChild(spinner);
        } else {
            spinner.classList.remove('d-none');
        }
    }

    // Crear FormData para enviar la solicitud DELETE
    const formData = new FormData();
    formData.append('_method', 'DELETE');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch(`/eventos/${eventoId}`, {
        method: 'POST', // Usamos POST con _method=DELETE para mayor compatibilidad
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
                message: 'Evento eliminado exitosamente'
            };
        }
    })
    .then(result => {
        if (result.success) {
            showNotification('Evento eliminado exitosamente', 'success');
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

            // Cambiar a vista mensual después de eliminar
            if (calendar) {
                calendar.changeView('dayGridMonth');
            }

            // Recargar eventos
            loadEventosAjax();
        } else {
            showNotification(result.message || 'Error al eliminar evento', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(`Error al eliminar evento: ${error.message}`, 'error');
    })
    .finally(() => {
        // Re-habilitar todos los elementos del modal
        allInputs.forEach(el => el.disabled = false);
        if (closeButton) closeButton.disabled = false;

        // Re-habilitar el botón y ocultar spinner
        if (deleteButton) {
            if (spinner) spinner.classList.add('d-none');
            if (btnText) btnText.textContent = 'Eliminar';
        }
    });
}

</script>
