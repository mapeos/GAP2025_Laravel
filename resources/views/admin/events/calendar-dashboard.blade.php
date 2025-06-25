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
}

.calendar-day.other-month {
    background: #f8f9fa;
    color: #6c757d;
}

.calendar-day.today {
    background: #e3f2fd;
    font-weight: bold;
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

function renderCalendar() {
    const grid = document.getElementById('calendar-grid');
    const currentMonth = document.getElementById('current-month');
    
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
        
        // Agregar número del día
        dayElement.innerHTML = `<div style="font-weight: bold; margin-bottom: 5px;">${date.getDate()}</div>`;
        
        grid.appendChild(dayElement);
    }
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
    renderCalendar();
});
</script> 