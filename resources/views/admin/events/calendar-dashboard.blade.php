<div id="dashboard-calendar"></div>
<script type="application/json" id="dashboard-events-json">@json($eventos ?? [])</script>
<script>
function getDashboardEventos() {
    var el = document.getElementById('dashboard-events-json');
    if (!el) return [];
    try {
        return JSON.parse(el.textContent);
    } catch (e) {
        return [];
    }
}
function initDashboardCalendar() {
    var calendarEl = document.getElementById('dashboard-calendar');
    if (!calendarEl || typeof window.FullCalendar === 'undefined' || typeof window.FullCalendar.Calendar === 'undefined') {
        setTimeout(initDashboardCalendar, 100);
        return;
    }
    calendarEl.innerHTML = "";
    var eventos = getDashboardEventos();
    eventos = eventos.map(function(ev) {
        if (ev.tipoEvento && ev.tipoEvento.color) {
            ev.color = ev.tipoEvento.color;
        } else if (ev.extendedProps && ev.extendedProps.color) {
            ev.color = ev.extendedProps.color;
        }
        return ev;
    });
    var calendar = new window.FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 400,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: eventos,
        eventClick: function(info) {
            window.location.href = '/admin/events/' + info.event.id;
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        dayMaxEvents: false,
        eventDidMount: function(info) {
            if (info.event.color) {
                info.el.style.backgroundColor = info.event.color;
                info.el.style.borderColor = info.event.color;
            }
        }
    });
    calendar.render();
}
// Siempre inicializa el calendario al cargar este script
initDashboardCalendar();
document.addEventListener("livewire:load", function() {
    initDashboardCalendar();
});
document.addEventListener("livewire:navigated", function() {
    initDashboardCalendar();
});
</script>