<!-- Calendar CSS -->
<style>
.calendar-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.calendar-nav {
    display: flex;
    gap: 10px;
}

.calendar-nav button {
    background: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
}

.calendar-nav button:hover {
    background: #0056b3;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    overflow: hidden;
}

.calendar-day-header {
    background: #f8f9fa;
    padding: 10px;
    text-align: center;
    font-weight: bold;
    border-bottom: 1px solid #dee2e6;
}

.calendar-day {
    background: white;
    min-height: 80px;
    padding: 5px;
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    position: relative;
    cursor: pointer;
    transition: background-color 0.2s;
}

.calendar-day:hover {
    background: #f8f9fa;
}

.calendar-day.other-month {
    background: #f8f9fa;
    color: #6c757d;
}

.calendar-day.today {
    background: #e3f2fd;
    font-weight: bold;
}

.calendar-day.has-events {
    background: #fff3cd;
}

.calendar-day.has-events:hover {
    background: #ffeaa7;
}

.event-indicator {
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%);
    width: 6px;
    height: 6px;
    background: #007bff;
    border-radius: 50%;
}

.event-indicator.multiple {
    width: 8px;
    height: 8px;
    background: #dc3545;
}

.event-tooltip {
    position: absolute;
    background: #333;
    color: white;
    padding: 5px 8px;
    border-radius: 4px;
    font-size: 12px;
    z-index: 1000;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.calendar-day:hover .event-tooltip {
    opacity: 1;
}
</style>

<div class="calendar-container">
    <div class="calendar-header">
        <h4 id="current-month">Calendario</h4>
        <div class="calendar-nav">
            <button onclick="previousMonth()">‹</button>
            <button onclick="today()">Hoy</button>
            <button onclick="nextMonth()">›</button>
        </div>
    </div>
    
    <div class="calendar-grid" id="calendar-grid">
        <!-- Los días se generarán dinámicamente -->
    </div>
</div>

<script>
let currentDate = new Date();
let eventos = @json($eventos ?? []);

// Función para cargar eventos vía AJAX
function loadEventos() {
    fetch('/events/json')
        .then(response => response.json())
        .then(data => {
            eventos = data;
            renderCalendar();
        })
        .catch(error => {
            console.error('Error cargando eventos:', error);
            renderCalendar(); // Renderizar sin eventos si hay error
        });
}

// Función para obtener eventos de una fecha específica
function getEventosForDate(date) {
    return eventos.filter(evento => {
        const eventoDate = new Date(evento.start);
        return eventoDate.toDateString() === date.toDateString();
    });
}

function renderCalendar() {
    const grid = document.getElementById('calendar-grid');
    const currentMonth = document.getElementById('current-month');
    
    if (!grid || !currentMonth) return;
    
    // Limpiar grid
    grid.innerHTML = '';
    
    // Actualizar título del mes
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    currentMonth.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    
    // Agregar headers de días
    const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    dayNames.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        grid.appendChild(dayHeader);
    });
    
    // Obtener primer día del mes y último día
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    // Generar días del calendario
    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        
        // Verificar si es otro mes
        if (date.getMonth() !== currentDate.getMonth()) {
            dayElement.classList.add('other-month');
        }
        
        // Verificar si es hoy
        const today = new Date();
        if (date.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
        }
        
        // Obtener eventos para este día
        const eventosDelDia = getEventosForDate(date);
        if (eventosDelDia.length > 0) {
            dayElement.classList.add('has-events');
            
            // Crear indicador de eventos
            const eventIndicator = document.createElement('div');
            eventIndicator.className = eventosDelDia.length > 1 ? 'event-indicator multiple' : 'event-indicator';
            dayElement.appendChild(eventIndicator);
            
            // Crear tooltip con información de eventos
            const tooltip = document.createElement('div');
            tooltip.className = 'event-tooltip';
            tooltip.textContent = eventosDelDia.map(e => e.title).join(', ');
            dayElement.appendChild(tooltip);
            
            // Agregar evento de clic para mostrar modal
            dayElement.addEventListener('click', () => {
                showEventosModal(date, eventosDelDia);
            });
        }
        
        // Agregar número del día
        dayElement.innerHTML = `<div style="font-weight: bold; margin-bottom: 5px;">${date.getDate()}</div>` + dayElement.innerHTML;
        
        grid.appendChild(dayElement);
    }
}

function showEventosModal(date, eventos) {
    // Crear modal dinámicamente
    const modalId = 'eventosModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = modalId;
        modal.className = 'modal fade';
        modal.setAttribute('tabindex', '-1');
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Eventos del ${date.toLocaleDateString('es-ES', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="eventosList"></div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Llenar lista de eventos
    const eventosList = modal.querySelector('#eventosList');
    eventosList.innerHTML = '';
    
    if (eventos.length === 0) {
        eventosList.innerHTML = '<p class="text-muted">No hay eventos para este día.</p>';
    } else {
        eventos.forEach(evento => {
            const eventoDiv = document.createElement('div');
            eventoDiv.className = 'card mb-2';
            eventoDiv.innerHTML = `
                <div class="card-body">
                    <h6 class="card-title">${evento.title}</h6>
                    <p class="card-text">${evento.extendedProps.descripcion || 'Sin descripción'}</p>
                    <small class="text-muted">
                        ${new Date(evento.start).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}
                        ${evento.end ? ` - ${new Date(evento.end).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}` : ''}
                    </small>
                </div>
            `;
            eventosList.appendChild(eventoDiv);
        });
    }
    
    // Mostrar modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function today() {
    currentDate = new Date();
    renderCalendar();
}

// Inicializar calendario cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    loadEventos();
});

// Recargar eventos cuando se hace navigate (para Livewire)
document.addEventListener('navigate', function() {
    setTimeout(function() {
        loadEventos();
    }, 100);
});
</script> 