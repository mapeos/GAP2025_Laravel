<div class="d-flex gap-2 mb-3">
    @if(Auth::user()->hasRole('Paciente'))
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaAiModal">
            <i class="ri-calendar-add-line me-1"></i>
            Solicitar cita médica
        </button>
    @else
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaAiModal">
            <i class="ri-add-line me-1"></i>
            Nueva Cita
        </button>
    @endif
    
    @if(Auth::user()->hasRole('Facultativo'))
        <a href="{{ route('solicitud-cita.recibidas') }}" class="btn btn-info">
            <i class="ri-mail-line me-1"></i> Ver solicitudes recibidas
        </a>
    @else
        <a href="{{ route('solicitud-cita.index') }}" class="btn btn-info">
            <i class="ri-mail-line me-1"></i> Ver mis solicitudes
        </a>
    @endif

    <button id="btnAgendaView" class="btn btn-secondary">
        <i class="ri-list-check-line me-1"></i> Ver agenda
    </button>
</div>
