<style>
    #calendar {
        margin: 20px 0;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .fc-event {
        cursor: pointer;
    }
    .fc-event-title {
        font-weight: 500;
    }
    .fc-daygrid-event {
        padding: 2px 4px;
    }
    .fc-daygrid-day-number {
        font-size: 0.9em;
        padding: 4px;
    }
    .fc-toolbar-title {
        font-size: 1.5em !important;
        font-weight: 500;
    }
    .fc-button-primary {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
    }
    .fc-button-primary:hover {
        background-color: #0b5ed7 !important;
        border-color: #0a58ca !important;
    }
    .fc-button-primary:not(:disabled):active,
    .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #0a58ca !important;
        border-color: #0a53be !important;
    }

    /* Estilos para el indicador de carga */
    #calendar.loading {
        opacity: 0.7;
        position: relative;
    }

    #calendar.loading::after {
        content: "Cargando eventos...";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 255, 255, 0.8);
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        font-weight: bold;
        color: #0d6efd;
    }
    .fc-event-resizing {
        opacity: 0.8;
    }
    /* Estilos para la vista de agenda */
    #agendaView .list-group-item.list-group-item-secondary {
        transition: background-color 0.2s;
    }
    #agendaView .list-group-item.list-group-item-secondary:hover {
        background-color: #6c757d;
        color: white;
    }
    #agendaView .events-container {
        transition: max-height 0.3s ease-in-out;
        overflow: hidden;
    }
    #agendaView .events-container.d-none {
        max-height: 0;
    }
    #agendaView .events-container:not(.d-none) {
        max-height: 1000px;
    }

    /* Estilos personalizados para Flatpickr */
    .flatpickr-input {
        cursor: pointer;
    }
    .flatpickr-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .flatpickr-calendar {
        font-family: inherit;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .flatpickr-day.selected {
        background: #0d6efd;
        border-color: #0d6efd;
    }
    .flatpickr-day.selected:hover {
        background: #0b5ed7;
        border-color: #0b5ed7;
    }
    .flatpickr-day.today {
        border-color: #0d6efd;
    }
    .flatpickr-day.today:hover {
        background: #e7f1ff;
    }

    /* Estilos para el botón flotante */
    .floating-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .floating-button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .floating-button i {
        font-size: 24px;
    }

    /* Estilos para el botón de volver a mes */
    .fc-toolbar .btn-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
        transition: all 0.2s ease;
    }

    .fc-toolbar .btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
        transform: translateY(-1px);
    }

    /* Mejorar la navegación del calendario */
    .fc-toolbar-chunk {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Estilos para eventos en vista de día */
    .fc-timegrid-event {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .fc-timegrid-event:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Estilos para días en vista de mes */
    .fc-daygrid-day {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .fc-daygrid-day:hover {
        background-color: #f8f9fa;
    }

    .fc-daygrid-day.fc-day-today {
        background-color: #e3f2fd !important;
    }

    /* Estilos para la vista de agenda */
    #agendaView {
        margin-bottom: 20px;
    }

    #agendaList .list-group-item {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    #agendaList .list-group-item:not(.list-group-item-secondary):hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    #agendaList .badge {
        font-weight: normal;
    }

    /* Estilos para el indicador de carga */
    #calendar.loading {
        opacity: 0.7;
        position: relative;
    }

    #calendar.loading::after {
        content: "Cargando eventos...";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 255, 255, 0.8);
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        font-weight: bold;
        color: #0d6efd;
    }
</style>
