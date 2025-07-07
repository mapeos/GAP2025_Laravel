<!-- Modal Solicitud Cita Médica - Flujo SERGAS -->
<div class="modal fade" id="solicitudCitaMedicaModal" tabindex="-1" aria-labelledby="solicitudCitaMedicaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="solicitudCitaMedicaModalLabel">
                    <i class="ri-heart-pulse-line me-2"></i>Solicitar Cita Médica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="solicitudCitaMedicaForm" action="{{ route('facultativo.cita.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tipo_sistema" value="medico">
                <input type="hidden" name="alumno_id" id="alumno_id" required>
                <input type="hidden" name="facultativo_id" id="facultativo_id" required>
                <input type="hidden" name="especialidad_id" id="especialidad_id" required>
                <input type="hidden" name="tratamiento_id" id="tratamiento_id">
                <input type="hidden" name="fecha_propuesta" id="fecha_propuesta" required>
                <input type="hidden" name="hora_propuesta" id="hora_propuesta" required>
                <input type="hidden" name="motivo" id="motivo" required>
                <input type="hidden" name="estado" value="pendiente">
                
                <div class="modal-body">
                    <!-- Paso 1: Seleccionar Paciente -->
                    <div id="step-1" class="step-content">
                        <div class="text-center mb-4">
                            <h6 class="text-success">
                                <i class="ri-user-line me-2"></i>Paso 1: Seleccionar Paciente
                            </h6>
                        </div>
                        <div class="mb-3">
                            <label for="paciente_select" class="form-label">Paciente *</label>
                            <select class="form-select" id="paciente_select" required>
                                <option value="">Seleccionar paciente</option>
                                @foreach($pacientes ?? [] as $paciente)
                                    <option value="{{ $paciente->id }}">{{ $paciente->name }} - {{ $paciente->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(Auth::user()->hasRole('Administrador'))
                        <div class="mb-3">
                            <label for="facultativo_select" class="form-label">Facultativo *</label>
                            <select class="form-select" id="facultativo_select" required>
                                <option value="">Seleccionar facultativo</option>
                                @foreach($facultativos ?? [] as $facultativo)
                                    <option value="{{ $facultativo->id }}">{{ $facultativo->user->name }} - {{ $facultativo->especialidad->nombre ?? 'Sin especialidad' }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>

                    <!-- Paso 2: Seleccionar Especialidad/Servicio -->
                    <div id="step-2" class="step-content" style="display: none;">
                        <div class="text-center mb-4">
                            <h6 class="text-success">
                                <i class="ri-stethoscope-line me-2"></i>Paso 2: Seleccionar Especialidad
                            </h6>
                        </div>
                        <div class="row">
                            @foreach($especialidades ?? [] as $especialidad)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 especialidad-card" data-especialidad-id="{{ $especialidad->id }}" style="cursor: pointer; border: 2px solid transparent;">
                                        <div class="card-body text-center">
                                            <div class="mb-2">
                                                <span class="badge" style="background-color: {{ $especialidad->color }}; color: white; font-size: 0.9em;">
                                                    {{ $especialidad->nombre }}
                                                </span>
                                            </div>
                                            @if($especialidad->descripcion)
                                                <small class="text-muted">{{ $especialidad->descripcion }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Paso 3: Seleccionar Prestación -->
                    <div id="step-3" class="step-content" style="display: none;">
                        <div class="text-center mb-4">
                            <h6 class="text-success">
                                <i class="ri-capsule-line me-2"></i>Paso 3: Seleccionar Prestación
                            </h6>
                            <p class="text-muted" id="especialidad-seleccionada"></p>
                        </div>
                        <div id="prestaciones-container" class="row">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>

                    <!-- Paso 4: Seleccionar Fecha -->
                    <div id="step-4" class="step-content" style="display: none;">
                        <div class="text-center mb-4">
                            <h6 class="text-success">
                                <i class="ri-calendar-line me-2"></i>Paso 4: Seleccionar Fecha
                            </h6>
                            <p class="text-muted" id="prestacion-seleccionada"></p>
                        </div>
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="calendar-container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="prevMonth">
                                            <i class="ri-arrow-left-s-line"></i>
                                        </button>
                                        <h6 id="currentMonth" class="mb-0"></h6>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="nextMonth">
                                            <i class="ri-arrow-right-s-line"></i>
                                        </button>
                                    </div>
                                    <div id="calendar" class="calendar-grid">
                                        <!-- Se generará dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 5: Seleccionar Hora -->
                    <div id="step-5" class="step-content" style="display: none;">
                        <div class="text-center mb-4">
                            <h6 class="text-success">
                                <i class="ri-time-line me-2"></i>Paso 5: Seleccionar Hora
                            </h6>
                            <p class="text-muted" id="fecha-seleccionada"></p>
                        </div>
                        <div id="horarios-container" class="row">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>

                    <!-- Paso 6: Motivo de la Consulta -->
                    <div id="step-6" class="step-content" style="display: none;">
                        <div class="text-center mb-4">
                            <h6 class="text-success">
                                <i class="ri-chat-1-line me-2"></i>Paso 6: Motivo de la Consulta
                            </h6>
                        </div>
                        <div class="mb-3">
                            <label for="motivo_textarea" class="form-label">Motivo de la consulta *</label>
                            <textarea class="form-control" id="motivo_textarea" rows="4" required 
                                      placeholder="Describe el motivo principal de la consulta..."></textarea>
                        </div>
                    </div>

                    <!-- Resumen Final -->
                    <div id="step-7" class="step-content" style="display: none;">
                        <div class="text-center mb-4">
                            <h6 class="text-success">
                                <i class="ri-check-line me-2"></i>Resumen de la Cita
                            </h6>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Paciente:</strong> <span id="resumen-paciente"></span></p>
                                        <p><strong>Especialidad:</strong> <span id="resumen-especialidad"></span></p>
                                        <p><strong>Prestación:</strong> <span id="resumen-prestacion"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Fecha:</strong> <span id="resumen-fecha"></span></p>
                                        <p><strong>Hora:</strong> <span id="resumen-hora"></span></p>
                                        <p><strong>Motivo:</strong> <span id="resumen-motivo"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnAnterior" style="display: none;">
                        <i class="ri-arrow-left-line me-1"></i>Anterior
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-success" id="btnSiguiente">
                        <span>Siguiente</span> <i class="ri-arrow-right-line ms-1"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="btnEnviar" style="display: none;">
                        <i class="ri-send-plane-line me-1"></i>Confirmar Cita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.especialidad-card:hover {
    border-color: #198754 !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.especialidad-card.selected {
    border-color: #198754 !important;
    background-color: #f8fff9;
}

.prestacion-card {
    cursor: pointer;
    border: 2px solid transparent;
}

.prestacion-card:hover {
    border-color: #198754 !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.prestacion-card.selected {
    border-color: #198754 !important;
    background-color: #f8fff9;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 5px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    cursor: pointer;
    font-size: 0.9em;
}

.calendar-day:hover {
    background-color: #e9ecef;
}

.calendar-day.selected {
    background-color: #198754;
    color: white;
}

.calendar-day.disabled {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}

.calendar-day.available {
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.horario-btn {
    margin: 5px;
    min-width: 80px;
}

.horario-btn.selected {
    background-color: #198754;
    border-color: #198754;
}
</style>

<script>
let currentStep = 1;
let selectedData = {
    paciente: null,
    especialidad: null,
    prestacion: null,
    fecha: null,
    hora: null,
    motivo: null
};

const especialidades = @json($especialidades ?? []);
const tratamientos = @json($tratamientos ?? []);
const pacientes = @json($pacientes ?? []);

document.addEventListener('DOMContentLoaded', function() {
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnAnterior = document.getElementById('btnAnterior');
    const btnEnviar = document.getElementById('btnEnviar');
    
    btnSiguiente.addEventListener('click', nextStep);
    btnAnterior.addEventListener('click', previousStep);
    
    // Establecer el facultativo actual cuando se abre el modal
    const modal = document.getElementById('solicitudCitaMedicaModal');
    modal.addEventListener('show.bs.modal', function() {
        // Si el usuario es facultativo, establecer su ID
        @if(Auth::user()->hasRole('Facultativo'))
            const facultativo = @json(Auth::user()->facultativo ?? null);
            if (facultativo) {
                document.getElementById('facultativo_id').value = facultativo.id;
            }
        @elseif(Auth::user()->hasRole('Administrador'))
            // Para administradores, se seleccionará manualmente
            document.getElementById('facultativo_id').value = '';
        @endif
    });
    
    // Paso 1: Seleccionar paciente
    document.getElementById('paciente_select').addEventListener('change', function() {
        const pacienteId = this.value;
        if (pacienteId) {
            const paciente = pacientes.find(p => p.id == pacienteId);
            selectedData.paciente = paciente;
            document.getElementById('alumno_id').value = pacienteId;
        }
    });
    
    // Seleccionar facultativo (solo para administradores)
    @if(Auth::user()->hasRole('Administrador'))
    document.getElementById('facultativo_select').addEventListener('change', function() {
        const facultativoId = this.value;
        if (facultativoId) {
            document.getElementById('facultativo_id').value = facultativoId;
        }
    });
    @endif
    
    // Paso 2: Seleccionar especialidad
    document.querySelectorAll('.especialidad-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.especialidad-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            const especialidadId = this.dataset.especialidadId;
            const especialidad = especialidades.find(e => e.id == especialidadId);
            selectedData.especialidad = especialidad;
            document.getElementById('especialidad_id').value = especialidadId;
        });
    });
    
    // Inicializar calendario
    initCalendar();
});

function nextStep() {
    if (validateCurrentStep()) {
        currentStep++;
        showStep(currentStep);
        updateButtons();
    }
}

function previousStep() {
    currentStep--;
    showStep(currentStep);
    updateButtons();
}

function showStep(step) {
    // Ocultar todos los pasos
    for (let i = 1; i <= 7; i++) {
        document.getElementById(`step-${i}`).style.display = 'none';
    }
    
    // Mostrar el paso actual
    document.getElementById(`step-${step}`).style.display = 'block';
    
    // Cargar datos específicos del paso
    switch(step) {
        case 3:
            loadPrestaciones();
            break;
        case 4:
            updateCalendar();
            break;
        case 5:
            loadHorarios();
            break;
        case 7:
            updateResumen();
            break;
    }
}

function validateCurrentStep() {
    switch(currentStep) {
        case 1:
            if (!selectedData.paciente) {
                alert('Por favor selecciona un paciente');
                return false;
            }
            @if(Auth::user()->hasRole('Administrador'))
            if (!document.getElementById('facultativo_id').value) {
                alert('Por favor selecciona un facultativo');
                return false;
            }
            @endif
            break;
        case 2:
            if (!selectedData.especialidad) {
                alert('Por favor selecciona una especialidad');
                return false;
            }
            break;
        case 3:
            if (!selectedData.prestacion) {
                alert('Por favor selecciona una prestación');
                return false;
            }
            break;
        case 4:
            if (!selectedData.fecha) {
                alert('Por favor selecciona una fecha');
                return false;
            }
            break;
        case 5:
            if (!selectedData.hora) {
                alert('Por favor selecciona una hora');
                return false;
            }
            break;
        case 6:
            const motivo = document.getElementById('motivo_textarea').value.trim();
            if (!motivo) {
                alert('Por favor describe el motivo de la consulta');
                return false;
            }
            selectedData.motivo = motivo;
            document.getElementById('motivo').value = motivo;
            break;
    }
    return true;
}

function updateButtons() {
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnAnterior = document.getElementById('btnAnterior');
    const btnEnviar = document.getElementById('btnEnviar');
    
    if (currentStep === 1) {
        btnAnterior.style.display = 'none';
    } else {
        btnAnterior.style.display = 'inline-block';
    }
    
    if (currentStep === 7) {
        btnSiguiente.style.display = 'none';
        btnEnviar.style.display = 'inline-block';
    } else {
        btnSiguiente.style.display = 'inline-block';
        btnEnviar.style.display = 'none';
    }
}

function loadPrestaciones() {
    const container = document.getElementById('prestaciones-container');
    const especialidadSeleccionada = document.getElementById('especialidad-seleccionada');
    
    especialidadSeleccionada.textContent = selectedData.especialidad.nombre;
    
    // Filtrar tratamientos por especialidad
    const prestaciones = tratamientos.filter(t => t.especialidad_id == selectedData.especialidad.id);
    
    container.innerHTML = '';
    
    prestaciones.forEach(prestacion => {
        const col = document.createElement('div');
        col.className = 'col-md-6 mb-3';
        col.innerHTML = `
            <div class="card h-100 prestacion-card" data-prestacion-id="${prestacion.id}">
                <div class="card-body text-center">
                    <h6 class="card-title">${prestacion.nombre}</h6>
                    ${prestacion.descripcion ? `<p class="card-text small text-muted">${prestacion.descripcion}</p>` : ''}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-info">${prestacion.duracion_minutos ? (Math.floor(prestacion.duracion_minutos / 60) > 0 ? Math.floor(prestacion.duracion_minutos / 60) + 'h ' + (prestacion.duracion_minutos % 60) + 'min' : prestacion.duracion_minutos + ' min') : 'No especificada'}</span>
                        <span class="text-success fw-bold">${prestacion.costo ? '€' + parseFloat(prestacion.costo).toFixed(2) : 'No especificado'}</span>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(col);
        
        // Agregar evento click
        col.querySelector('.prestacion-card').addEventListener('click', function() {
            document.querySelectorAll('.prestacion-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedData.prestacion = prestacion;
            document.getElementById('tratamiento_id').value = prestacion.id;
        });
    });
}

function initCalendar() {
    const calendar = document.getElementById('calendar');
    const currentMonth = document.getElementById('currentMonth');
    
    let currentDate = new Date();
    let currentMonthDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    
    function renderCalendar() {
        const year = currentMonthDate.getFullYear();
        const month = currentMonthDate.getMonth();
        
        currentMonth.textContent = new Date(year, month).toLocaleDateString('es-ES', { 
            month: 'long', 
            year: 'numeric' 
        });
        
        calendar.innerHTML = '';
        
        // Días de la semana
        const daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        daysOfWeek.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day fw-bold';
            dayHeader.textContent = day;
            calendar.appendChild(dayHeader);
        });
        
        // Días del mes
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - (firstDay.getDay() || 7) + 1);
        
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            
            if (date.getMonth() === month) {
                dayElement.textContent = date.getDate();
                
                // Verificar si la fecha es válida (futura y no domingo)
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const isFuture = date >= today;
                const isNotSunday = date.getDay() !== 0;
                
                if (isFuture && isNotSunday) {
                    dayElement.classList.add('available');
                    dayElement.addEventListener('click', () => selectDate(date));
                } else {
                    dayElement.classList.add('disabled');
                }
            }
            
            calendar.appendChild(dayElement);
        }
    }
    
    // Eventos para cambiar mes
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentMonthDate.setMonth(currentMonthDate.getMonth() - 1);
        renderCalendar();
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentMonthDate.setMonth(currentMonthDate.getMonth() + 1);
        renderCalendar();
    });
    
    renderCalendar();
}

function selectDate(date) {
    document.querySelectorAll('.calendar-day').forEach(day => day.classList.remove('selected'));
    event.target.classList.add('selected');
    
    selectedData.fecha = date;
    document.getElementById('fecha-seleccionada').textContent = date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function loadHorarios() {
    const container = document.getElementById('horarios-container');
    container.innerHTML = '';
    
    // Horarios disponibles (puedes ajustar según tus necesidades)
    const horarios = [
        '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '12:00', '12:30', '16:00', '16:30', '17:00', '17:30',
        '18:00', '18:30', '19:00', '19:30'
    ];
    
    horarios.forEach(horario => {
        const col = document.createElement('div');
        col.className = 'col-md-3 mb-2';
        col.innerHTML = `
            <button type="button" class="btn btn-outline-success horario-btn" data-hora="${horario}">
                ${horario}
            </button>
        `;
        container.appendChild(col);
        
        col.querySelector('.horario-btn').addEventListener('click', function() {
            document.querySelectorAll('.horario-btn').forEach(btn => btn.classList.remove('selected'));
            this.classList.add('selected');
            selectedData.hora = horario;
            document.getElementById('hora_propuesta').value = horario;
        });
    });
}

function updateResumen() {
    document.getElementById('resumen-paciente').textContent = selectedData.paciente.name;
    document.getElementById('resumen-especialidad').textContent = selectedData.especialidad.nombre;
    document.getElementById('resumen-prestacion').textContent = selectedData.prestacion.nombre;
    document.getElementById('resumen-fecha').textContent = selectedData.fecha.toLocaleDateString('es-ES');
    document.getElementById('resumen-hora').textContent = selectedData.hora;
    document.getElementById('resumen-motivo').textContent = selectedData.motivo;
    
    // Combinar fecha y hora para el campo hidden
    const fechaHora = new Date(selectedData.fecha);
    const [hora, minuto] = selectedData.hora.split(':');
    fechaHora.setHours(parseInt(hora), parseInt(minuto), 0, 0);
    document.getElementById('fecha_propuesta').value = fechaHora.toISOString().slice(0, 10);
}

// Validación del formulario
document.getElementById('solicitudCitaMedicaForm').addEventListener('submit', function(e) {
    if (!selectedData.paciente || !selectedData.especialidad || !selectedData.prestacion || 
        !selectedData.fecha || !selectedData.hora || !selectedData.motivo) {
        e.preventDefault();
        alert('Por favor completa todos los pasos antes de enviar la solicitud.');
        return false;
    }
});
</script> 