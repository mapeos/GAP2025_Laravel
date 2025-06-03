@extends('template.base')

@section('content')
<div class="container">
    <h1 class="mb-4">Calendario de eventos</h1>
    <div id="calendar"></div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: @json($eventos ?? []),
                eventClick: function(info) {
                    window.location.href = '/events/' + info.event.id;
                }
            });
            calendar.render();
        });
    </script>
@endpush
