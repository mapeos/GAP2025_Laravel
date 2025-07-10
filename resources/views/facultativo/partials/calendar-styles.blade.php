<!-- Calendar Styles -->
<style>
/* Estilos para el calendario */
#calendar {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
}

/* Estilos para los datepickers */
.datepicker {
    background-color: #fff !important;
    cursor: pointer;
}

.datepicker:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

/* Estilos para Flatpickr */
.flatpickr-calendar {
    font-family: 'Source Sans 3', sans-serif;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border: 1px solid #e9ecef;
}

.flatpickr-day.selected {
    background: #0d6efd !important;
    border-color: #0d6efd !important;
}

.flatpickr-day.selected:hover {
    background: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
}

.flatpickr-day.today {
    border-color: #0d6efd;
    color: #0d6efd;
}

.flatpickr-day.today:hover {
    background: #0d6efd;
    color: white;
}

.flatpickr-months .flatpickr-month {
    background: #0d6efd;
    color: white;
    border-radius: 8px 8px 0 0;
}

.flatpickr-current-month .flatpickr-monthDropdown-months {
    color: white;
}

.flatpickr-current-month .flatpickr-monthDropdown-months option {
    color: #333;
}

.flatpickr-weekdays {
    background: #f8f9fa;
}

.flatpickr-weekday {
    color: #6c757d;
    font-weight: 600;
}

/* Estilos para campos readonly */
input[readonly].datepicker {
    background-color: #f8f9fa;
    cursor: pointer;
}

input[readonly].datepicker:hover {
    background-color: #e9ecef;
}

/* Estilos para el calendario principal */
.fc {
    font-family: 'Source Sans 3', sans-serif;
}

.fc-toolbar-title {
    font-weight: 600;
    color: #333;
}

.fc-button-primary {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
}

.fc-button-primary:hover {
    background-color: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
}

.fc-button-primary:focus {
    background-color: #0b5ed7 !important;
    border-color: #0b5ed7 !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.fc-button-primary:not(:disabled):active {
    background-color: #0a58ca !important;
    border-color: #0a58ca !important;
}

.fc-daygrid-day.fc-day-today {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.fc-event {
    border-radius: 4px;
    border: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.fc-event-title {
    font-weight: 600;
}

/* Estilos para eventos médicos */
.fc-event.consulta {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.fc-event.especialidad {
    background-color: #6f42c1;
    border-color: #6f42c1;
}

.fc-event.urgencia {
    background-color: #dc3545;
    border-color: #dc3545;
}

.fc-event.seguimiento {
    background-color: #fd7e14;
    border-color: #fd7e14;
}

.fc-event.revision {
    background-color: #20c997;
    border-color: #20c997;
}

/* Estilos para la vista de agenda */
#agendaView {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

#agendaView .card-header {
    background: #6c757d;
    color: white;
    border-radius: 8px 8px 0 0;
}

.list-group-item {
    border-left: none;
    border-right: none;
    border-radius: 0;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

/* Estilos para loading */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Estilos para tooltips */
.fc-event {
    cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
    .fc-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
    
    .datepicker {
        font-size: 16px; /* Evita zoom en iOS */
    }
}

/* Estilos para modales */
.modal-xl {
    max-width: 1140px;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Estilos para pestañas */
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
    background: none;
}

.nav-tabs .nav-link:hover {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
}

/* Estilos para secciones del formulario */
.text-primary.border-bottom {
    border-bottom: 2px solid #0d6efd !important;
    color: #0d6efd !important;
}

/* Estilos para campos de formulario */
.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Estilos para botones */
.btn-success {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-success:hover {
    background-color: #0b5ed7;
    border-color: #0b5ed7;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0b5ed7;
}
</style>
