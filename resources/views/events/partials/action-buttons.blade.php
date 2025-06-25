<div class="d-flex gap-2 mb-3">
    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearEventoModal">
            <i class="ri-add-line me-1"></i>
            Crear nuevo evento
        </button>
    @else
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearEventoModal">
            <i class="ri-add-line me-1"></i>
            Crear recordatorio personal
        </button>
    @endif

    @if(Auth::user()->hasRole('Alumno'))
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaModal">
            <i class="ri-user-line me-1"></i>
            Solicitar cita/consulta con profesor
        </button>
    @else
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#solicitudCitaModal2">
            <i class="ri-robot-line me-1"></i>
            Agendar cita/consulta con IA
        </button>
    @endif
    
    @if(Auth::user()->hasRole('profesor'))
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