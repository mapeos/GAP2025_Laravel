<!-- Modal Solicitar Cita - Multi-paso -->
<div class="modal fade" id="solicitarCitaModal" tabindex="-1" aria-labelledby="solicitarCitaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitarCitaModalLabel">
                    <i class="ri-calendar-add-line me-2"></i>Solicitar Cita Médica
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Indicador de progreso -->
                <div class="progress mb-4" style="height: 4px;">
                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 33%"></div>
                </div>

                <!-- Paso 1: Seleccionar Especialidad -->
                <div id="step1" class="step-content">
                    <h6 class="mb-3"><i class="ri-stethoscope-line me-2"></i>Paso 1: Seleccionar Especialidad</h6>
                    
                    <div class="mb-3">
                        <label for="especialidad_id" class="form-label">Especialidad Médica *</label>
                        <select class="form-select" id="especialidad_id" name="especialidad_id" required>
                            <option value="">Seleccionar especialidad</option>
                            @foreach($especialidades as $especialidad)
                                <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Paso 2: Seleccionar Prestación -->
                <div id="step2" class="step-content" style="display: none;">
                    <h6 class="mb-3"><i class="ri-heart-pulse-line me-2"></i>Paso 2: Seleccionar Prestación</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Prestaciones Disponibles</label>
                        <div id="prestacionesContainer">
                            <div class="alert alert-info">
                                <i class="ri-information-line me-2"></i>
                                Selecciona una especialidad para ver las prestaciones disponibles.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Fecha y Hora -->
                <div id="step3" class="step-content" style="display: none;">
                    <h6 class="mb-3"><i class="ri-calendar-time-line me-2"></i>Paso 3: Fecha y Hora</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_propuesta" class="form-label">Fecha *</label>
                                <input type="date" class="form-control" id="fecha_propuesta" name="fecha_propuesta" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_propuesta" class="form-label">Hora *</label>
                                <select class="form-select" id="hora_propuesta" name="hora_propuesta" required>
                                    <option value="">Seleccionar hora</option>
                                    <option value="08:00">08:00</option>
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="11:00">11:00</option>
                                    <option value="12:00">12:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="15:00">15:00</option>
                                    <option value="16:00">16:00</option>
                                    <option value="17:00">17:00</option>
                                    <option value="18:00">18:00</option>
                                    <option value="19:00">19:00</option>
                                    <option value="20:00">20:00</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de la Consulta *</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required
                                  placeholder="Describe brevemente el motivo de tu consulta..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                    <i class="ri-arrow-left-line me-1"></i>Anterior
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="nextBtn">
                    Siguiente<i class="ri-arrow-right-line ms-1"></i>
                </button>
                <button type="button" class="btn btn-success" id="solicitarBtn" style="display: none;">
                    <i class="ri-send-plane-line me-1"></i>Solicitar Cita
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
let totalSteps = 3;
let selectedEspecialidad = null;
let selectedTratamiento = null;

// Función para mostrar/ocultar pasos
function showStep(step) {
    // Ocultar todos los pasos
    for (let i = 1; i <= totalSteps; i++) {
        document.getElementById(`step${i}`).style.display = 'none';
    }

    // Mostrar el paso actual
    document.getElementById(`step${step}`).style.display = 'block';

    // Actualizar barra de progreso
    const progress = (step / totalSteps) * 100;
    document.getElementById('progressBar').style.width = progress + '%';

    // Actualizar botones
    updateButtons();
}

// Función para actualizar botones
function updateButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const solicitarBtn = document.getElementById('solicitarBtn');

    // Botón anterior
    if (currentStep > 1) {
        prevBtn.style.display = 'inline-block';
    } else {
        prevBtn.style.display = 'none';
    }

    // Botón siguiente/solicitar
    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        solicitarBtn.style.display = 'inline-block';
    } else {
        nextBtn.style.display = 'inline-block';
        solicitarBtn.style.display = 'none';
    }
}

// Función para validar el paso actual
function validateCurrentStep() {
    if (currentStep === 1) {
        const especialidad = document.getElementById('especialidad_id').value;
        if (!especialidad) {
            alert('Por favor selecciona una especialidad.');
            return false;
        }
    } else if (currentStep === 2) {
        if (!selectedTratamiento) {
            alert('Por favor selecciona una prestación.');
            return false;
        }
    } else if (currentStep === 3) {
        const fecha = document.getElementById('fecha_propuesta').value;
        const hora = document.getElementById('hora_propuesta').value;
        const motivo = document.getElementById('motivo').value;

        if (!fecha) {
            alert('Por favor selecciona una fecha.');
            return false;
        }

        if (!hora) {
            alert('Por favor selecciona una hora.');
            return false;
        }

        if (!motivo.trim()) {
            alert('Por favor describe el motivo de la consulta.');
            return false;
        }
    }

    return true;
}

// Función para cargar prestaciones
async function loadPrestaciones(especialidadId) {
    try {
        const response = await fetch(`/paciente/tratamientos/${especialidadId}`);
        const tratamientos = await response.json();
        
        const container = document.getElementById('prestacionesContainer');
        container.innerHTML = '';
        
        if (tratamientos.length > 0) {
            tratamientos.forEach(tratamiento => {
                const div = document.createElement('div');
                div.className = 'form-check mb-2';
                div.innerHTML = `
                    <input class="form-check-input" type="radio" name="tratamiento_id" 
                           id="tratamiento_${tratamiento.id}" value="${tratamiento.id}">
                    <label class="form-check-label" for="tratamiento_${tratamiento.id}">
                        <strong>${tratamiento.nombre}</strong>
                        <small class="text-muted"> (${tratamiento.duracion_minutos} minutos)</small>
                    </label>
                `;
                container.appendChild(div);
            });
        } else {
            // Si no hay tratamientos específicos, mostrar opción de consulta general
            const div = document.createElement('div');
            div.className = 'form-check mb-2';
            div.innerHTML = `
                <input class="form-check-input" type="radio" name="tratamiento_id" 
                       id="tratamiento_consulta" value="consulta" checked>
                <label class="form-check-label" for="tratamiento_consulta">
                    <strong>Consulta General</strong>
                    <small class="text-muted"> (30 minutos)</small>
                </label>
            `;
            container.appendChild(div);
        }
        
        // Agregar event listeners a los radio buttons
        document.querySelectorAll('input[name="tratamiento_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedTratamiento = this.value;
            });
        });
        
    } catch (error) {
        console.error('Error cargando prestaciones:', error);
        const container = document.getElementById('prestacionesContainer');
        container.innerHTML = `
            <div class="alert alert-warning">
                <i class="ri-alert-line me-2"></i>
                Error al cargar las prestaciones. Por favor intenta de nuevo.
            </div>
        `;
    }
}

// Función para solicitar cita
async function solicitarCita() {
    const formData = {
        especialidad_id: document.getElementById('especialidad_id').value,
        tratamiento_id: selectedTratamiento,
        fecha_propuesta: document.getElementById('fecha_propuesta').value,
        hora_propuesta: document.getElementById('hora_propuesta').value,
        motivo: document.getElementById('motivo').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    try {
        const response = await fetch('/paciente/solicitar-cita', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': formData._token
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            alert('¡Solicitud enviada exitosamente! El médico revisará tu solicitud y te confirmará la cita.');
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('solicitarCitaModal'));
            modal.hide();
            
            // Recargar página para actualizar estadísticas
            location.reload();
        } else {
            alert('Error al enviar la solicitud: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error enviando solicitud:', error);
        alert('Error al enviar la solicitud. Por favor intenta de nuevo.');
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botón siguiente
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (!validateCurrentStep()) return;

        if (currentStep === 1) {
            // Cargar prestaciones para la especialidad seleccionada
            const especialidadId = document.getElementById('especialidad_id').value;
            loadPrestaciones(especialidadId);
        }

        currentStep++;
        showStep(currentStep);
    });

    // Botón anterior
    document.getElementById('prevBtn').addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });

    // Botón solicitar
    document.getElementById('solicitarBtn').addEventListener('click', solicitarCita);

    // Event listener para cambio de especialidad
    document.getElementById('especialidad_id').addEventListener('change', function() {
        selectedEspecialidad = this.value;
    });

    // Resetear modal cuando se cierre
    document.getElementById('solicitarCitaModal').addEventListener('hidden.bs.modal', function() {
        currentStep = 1;
        selectedEspecialidad = null;
        selectedTratamiento = null;
        showStep(1);
        
        // Limpiar formulario
        document.getElementById('especialidad_id').value = '';
        document.getElementById('fecha_propuesta').value = '';
        document.getElementById('hora_propuesta').value = '';
        document.getElementById('motivo').value = '';
        
        // Limpiar prestaciones
        document.getElementById('prestacionesContainer').innerHTML = `
            <div class="alert alert-info">
                <i class="ri-information-line me-2"></i>
                Selecciona una especialidad para ver las prestaciones disponibles.
            </div>
        `;
    });
});
</script> 