<!-- Modal Solicitud Cita con IA - Multi-paso -->
<div class="modal fade" id="solicitudCitaModal2" tabindex="-1" aria-labelledby="solicitudCitaAiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudCitaAiModalLabel">
                    <i class="ri-robot-line me-2"></i>Agendar Cita/Consulta con IA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <!-- Indicador de progreso -->
                <div class="progress mb-4" style="height: 4px;">
                    <div class="progress-bar" id="progressBar" role="progressbar" style="width: 25%"></div>
                </div>
                
                <!-- Paso 1: Selección de alumno, tipo de cita y descripción -->
                <div id="step1" class="step-content">
                    <h6 class="mb-3"><i class="ri-user-line me-2"></i>Paso 1: Información Básica</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ai_alumno_id" class="form-label">Alumno *</label>
                                <select class="form-select" id="ai_alumno_id" name="alumno_id" required>
                                    <option value="">Seleccionar alumno</option>
                                    @foreach($alumnos ?? [] as $alumno)
                                        <option value="{{ $alumno->id }}">{{ $alumno->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ai_tipo_consulta" class="form-label">Tipo de Consulta *</label>
                                <select class="form-select" id="ai_tipo_consulta" name="tipo_consulta" required>
                                    <option value="">Seleccionar tipo de consulta</option>
                                    <optgroup label="Consultas Académicas">
                                        <option value="tutoria" data-duracion="60">Tutoría académica (60 min)</option>
                                        <option value="revision_examen" data-duracion="30">Revisión de examen (30 min)</option>
                                        <option value="revision_proyecto" data-duracion="45">Revisión de proyecto (45 min)</option>
                                        <option value="duda_asignatura" data-duracion="30">Duda sobre asignatura (30 min)</option>
                                        <option value="orientacion_academica" data-duracion="60">Orientación académica (60 min)</option>
                                    </optgroup>
                                    <optgroup label="Consultas Específicas">
                                        <option value="problema_tecnico" data-duracion="30">Problema técnico (30 min)</option>
                                        <option value="planificacion_estudio" data-duracion="45">Planificación de estudio (45 min)</option>
                                        <option value="evaluacion_rendimiento" data-duracion="60">Evaluación de rendimiento (60 min)</option>
                                        <option value="consejo_carrera" data-duracion="90">Consejo de carrera (90 min)</option>
                                    </optgroup>
                                    <optgroup label="Otros">
                                        <option value="personalizado" data-duracion="60">Motivo personalizado (60 min)</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ai_motivo" class="form-label">Descripción Personalizada *</label>
                        <textarea class="form-control" id="ai_motivo" name="motivo" rows="3" required 
                                  placeholder="Describe detalladamente el motivo de la consulta para que la IA pueda sugerir el mejor horario..."></textarea>
                    </div>
                </div>

                <!-- Paso 2: Ajuste de duración y preferencias -->
                <div id="step2" class="step-content" style="display: none;">
                    <h6 class="mb-3"><i class="ri-time-line me-2"></i>Paso 2: Duración y Preferencias</h6>
                    
                    <!-- Indicador de duración actual -->
                    <div class="alert alert-info" id="duracionInfo">
                        <i class="ri-information-line me-2"></i>
                        Duración actual: <strong id="duracionActual">30 minutos</strong>
                    </div>
                    
                    <!-- Controles de duración -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-outline-secondary" id="btnReducirDuracion">
                                    <i class="ri-subtract-line"></i> -30 min
                                </button>
                                <span class="mx-3 fw-bold" id="duracionDisplay">30 min</span>
                                <button type="button" class="btn btn-outline-secondary" id="btnAumentarDuracion">
                                    <i class="ri-add-line"></i> +30 min
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="flexibilidadHora">
                                <label class="form-check-label" for="flexibilidadHora">
                                    Flexibilidad en horario (permitir ajustes de ±1 hora)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preferencias de fecha -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ai_fecha_preferida" class="form-label">Fecha preferida</label>
                                <input type="date" class="form-control" id="ai_fecha_preferida" name="fecha_preferida">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ai_hora_preferida" class="form-label">Hora preferida</label>
                                <select class="form-select" id="ai_hora_preferida" name="hora_preferida">
                                    <option value="">Cualquier hora</option>
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
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ai_prioridad" class="form-label">Prioridad</label>
                        <select class="form-select" id="ai_prioridad" name="prioridad">
                            <option value="baja">Baja</option>
                            <option value="normal" selected>Normal</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ai_preferencias" class="form-label">Preferencias adicionales</label>
                        <textarea class="form-control" id="ai_preferencias" name="preferencias" rows="2"
                                  placeholder="Horarios preferidos, restricciones, etc..."></textarea>
                    </div>
                </div>

                <!-- Paso 3: Sugerencias de IA -->
                <div id="step3" class="step-content" style="display: none;">
                    <h6 class="mb-3"><i class="ri-robot-line me-2"></i>Paso 3: Sugerencias de IA</h6>
                    <div id="loadingSuggestions" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">La IA está analizando la disponibilidad y generando sugerencias...</p>
                    </div>
                    
                    <div id="suggestionsContainer" style="display: none;">
                        <div class="alert alert-success">
                            <i class="ri-check-line me-2"></i>
                            La IA ha analizado la disponibilidad y sugiere los siguientes horarios:
                        </div>
                        
                        <div id="suggestionsList" class="row">
                            <!-- Las sugerencias se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                    
                    <div id="noSuggestions" class="alert alert-warning" style="display: none;">
                        <i class="ri-alert-line me-2"></i>
                        No se encontraron horarios disponibles en el rango solicitado. Intenta con otras fechas o duración.
                    </div>
                </div>

                <!-- Paso 4: Confirmación -->
                <div id="step4" class="step-content" style="display: none;">
                    <h6 class="mb-3"><i class="ri-check-double-line me-2"></i>Paso 4: Confirmar Cita</h6>
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Revisa los detalles de la cita antes de confirmar.
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Detalles de la cita:</h6>
                                    <p><strong>Alumno:</strong> <span id="confirmAlumno"></span></p>
                                    <p><strong>Tipo:</strong> <span id="confirmTipo"></span></p>
                                    <p><strong>Fecha:</strong> <span id="confirmFecha"></span></p>
                                    <p><strong>Hora:</strong> <span id="confirmHora"></span></p>
                                    <p><strong>Duración:</strong> <span id="confirmDuracion"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Información adicional:</h6>
                                    <p><strong>Motivo:</strong> <span id="confirmMotivo"></span></p>
                                    <p><strong>Prioridad:</strong> <span id="confirmPrioridad"></span></p>
                                    <p><strong>Flexibilidad:</strong> <span id="confirmFlexibilidad"></span></p>
                                </div>
                            </div>
                        </div>
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
                <button type="button" class="btn btn-success" id="confirmBtn" style="display: none;">
                    <i class="ri-check-line me-1"></i>Confirmar Cita
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para reset -->
<form id="solicitudCitaAiForm" style="display: none;">
    @csrf
</form>

<script>
let currentStep = 1;
let totalSteps = 4;
let selectedSuggestion = null;
let currentDuracion = 30;

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
    const confirmBtn = document.getElementById('confirmBtn');
    
    // Botón anterior
    if (currentStep > 1) {
        prevBtn.style.display = 'inline-block';
    } else {
        prevBtn.style.display = 'none';
    }
    
    // Botón siguiente/confirmar
    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        confirmBtn.style.display = 'inline-block';
    } else {
        nextBtn.style.display = 'inline-block';
        confirmBtn.style.display = 'none';
    }
    
    // Cambiar texto del botón siguiente en el paso 2
    if (currentStep === 2) {
        nextBtn.innerHTML = '<i class="ri-robot-line me-1"></i>Generar Sugerencias';
    } else if (currentStep === 3) {
        nextBtn.innerHTML = 'Siguiente<i class="ri-arrow-right-line ms-1"></i>';
    } else {
        nextBtn.innerHTML = 'Siguiente<i class="ri-arrow-right-line ms-1"></i>';
    }
}

// Función para validar el paso actual
function validateCurrentStep() {
    if (currentStep === 1) {
        const alumno = document.getElementById('ai_alumno_id').value;
        const tipoConsulta = document.getElementById('ai_tipo_consulta').value;
        const motivo = document.getElementById('ai_motivo').value;
        
        if (!alumno) {
            alert('Por favor selecciona un alumno.');
            return false;
        }
        
        if (!tipoConsulta) {
            alert('Por favor selecciona un tipo de consulta.');
            return false;
        }
        
        if (!motivo.trim()) {
            alert('Por favor describe el motivo de la consulta.');
            return false;
        }
    }
    
    return true;
}

// Función para actualizar duración basada en tipo de consulta
function updateDuracionFromTipoConsulta() {
    const tipoConsulta = document.getElementById('ai_tipo_consulta');
    
    if (tipoConsulta.value) {
        const selectedOption = tipoConsulta.options[tipoConsulta.selectedIndex];
        const duracion = selectedOption.getAttribute('data-duracion');
        
        if (duracion) {
            currentDuracion = parseInt(duracion);
            updateDuracionDisplay();
        }
    }
}

// Función para actualizar la visualización de duración
function updateDuracionDisplay() {
    document.getElementById('duracionDisplay').textContent = currentDuracion + ' min';
    document.getElementById('duracionActual').textContent = currentDuracion + ' minutos';
    
    // Actualizar estado de botones
    document.getElementById('btnReducirDuracion').disabled = currentDuracion <= 15;
    document.getElementById('btnAumentarDuracion').disabled = currentDuracion >= 180;
}

// Función para generar sugerencias
async function generateSuggestions() {
    const loadingDiv = document.getElementById('loadingSuggestions');
    const suggestionsContainer = document.getElementById('suggestionsContainer');
    const noSuggestions = document.getElementById('noSuggestions');
    
    // Mostrar loading
    loadingDiv.style.display = 'block';
    suggestionsContainer.style.display = 'none';
    noSuggestions.style.display = 'none';
    
    // Recopilar datos
    const data = {
        alumno_id: document.getElementById('ai_alumno_id').value,
        duracion: currentDuracion,
        tipo_consulta: document.getElementById('ai_tipo_consulta').value,
        fecha_preferida: document.getElementById('ai_fecha_preferida').value,
        hora_preferida: document.getElementById('ai_hora_preferida').value,
        motivo: document.getElementById('ai_motivo').value,
        prioridad: document.getElementById('ai_prioridad').value,
        preferencias: document.getElementById('ai_preferencias').value,
        flexibilidad: document.getElementById('flexibilidadHora').checked,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    try {
        const response = await fetch('/ai/appointments/suggest', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data)
        });
        
        // Check if response is ok
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error:', response.status, response.statusText);
            console.error('Response body:', errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        // Ocultar loading
        loadingDiv.style.display = 'none';
        
        if (result.success && result.suggestions && result.suggestions.length > 0) {
            displaySuggestions(result.suggestions);
            suggestionsContainer.style.display = 'block';
        } else {
            noSuggestions.style.display = 'block';
        }
    } catch (error) {
        console.error('Error generando sugerencias:', error);
        console.error('Request data:', data);
        loadingDiv.style.display = 'none';
        alert('Error al generar sugerencias: ' + error.message + '. Por favor intenta de nuevo.');
    }
}

// Función para mostrar sugerencias
function displaySuggestions(suggestions) {
    const container = document.getElementById('suggestionsList');
    container.innerHTML = '';
    
    suggestions.forEach((suggestion, index) => {
        const card = document.createElement('div');
        card.className = 'col-md-6 mb-3';
        card.innerHTML = `
            <div class="card suggestion-card" data-suggestion='${JSON.stringify(suggestion)}'>
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="ri-calendar-check-line me-2"></i>
                        Opción ${index + 1}
                    </h6>
                    <p class="card-text">
                        <strong>Fecha:</strong> ${suggestion.fecha}<br>
                        <strong>Hora:</strong> ${suggestion.hora}<br>
                        <strong>Duración:</strong> ${suggestion.duracion} minutos<br>
                        <strong>Confianza:</strong> 
                        <span class="badge bg-${getConfidenceColor(suggestion.confianza)}">
                            ${suggestion.confianza}%
                        </span>
                    </p>
                    <button class="btn btn-primary btn-sm select-suggestion" data-index="${index}">
                        <i class="ri-check-line me-1"></i>Seleccionar
                    </button>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
    
    // Agregar event listeners a los botones de selección
    document.querySelectorAll('.select-suggestion').forEach(button => {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            selectSuggestion(suggestions[index]);
        });
    });
}

// Función para obtener color de confianza
function getConfidenceColor(confianza) {
    if (confianza >= 80) return 'success';
    if (confianza >= 60) return 'warning';
    return 'danger';
}

// Función para seleccionar sugerencia
function selectSuggestion(suggestion) {
    selectedSuggestion = suggestion;
    
    // Actualizar información de confirmación
    document.getElementById('confirmAlumno').textContent = document.getElementById('ai_alumno_id').options[document.getElementById('ai_alumno_id').selectedIndex].text;
    document.getElementById('confirmTipo').textContent = document.getElementById('ai_tipo_consulta').options[document.getElementById('ai_tipo_consulta').selectedIndex].text;
    document.getElementById('confirmFecha').textContent = suggestion.fecha;
    document.getElementById('confirmHora').textContent = suggestion.hora;
    document.getElementById('confirmDuracion').textContent = suggestion.duracion + ' minutos';
    document.getElementById('confirmMotivo').textContent = document.getElementById('ai_motivo').value;
    document.getElementById('confirmPrioridad').textContent = document.getElementById('ai_prioridad').value;
    document.getElementById('confirmFlexibilidad').textContent = document.getElementById('flexibilidadHora').checked ? 'Sí' : 'No';
    
    // Ir al siguiente paso
    currentStep++;
    showStep(currentStep);
}

// Función para confirmar cita
async function confirmAppointment() {
    if (!selectedSuggestion) {
        alert('No se ha seleccionado ninguna sugerencia.');
        return;
    }
    
    const data = {
        alumno_id: document.getElementById('ai_alumno_id').value,
        tipo_consulta: document.getElementById('ai_tipo_consulta').value,
        fecha_inicio: selectedSuggestion.fecha_inicio,
        fecha_fin: selectedSuggestion.fecha_fin,
        motivo: document.getElementById('ai_motivo').value,
        duracion: selectedSuggestion.duracion,
        prioridad: document.getElementById('ai_prioridad').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    try {
        const response = await fetch('/ai/appointments/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('¡Cita creada exitosamente!');
            // Cerrar modal y recargar calendario
            const modal = bootstrap.Modal.getInstance(document.getElementById('solicitudCitaModal2'));
            modal.hide();
            location.reload();
        } else {
            alert('Error al crear la cita: ' + (result.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error creando cita:', error);
        alert('Error al crear la cita. Por favor intenta de nuevo.');
    }
}

// Función para resetear el modal
function resetModal() {
    currentStep = 1;
    selectedSuggestion = null;
    currentDuracion = 30;
    showStep(1);
    
    // Limpiar formulario
    document.getElementById('solicitudCitaAiForm').reset();
    document.getElementById('ai_alumno_id').value = '';
    document.getElementById('ai_tipo_consulta').value = '';
    document.getElementById('ai_motivo').value = '';
    document.getElementById('ai_fecha_preferida').value = '';
    document.getElementById('ai_hora_preferida').value = '';
    document.getElementById('ai_prioridad').value = 'normal';
    document.getElementById('ai_preferencias').value = '';
    document.getElementById('flexibilidadHora').checked = false;
    
    // Resetear duración
    updateDuracionDisplay();
    
    // Ocultar contenedores de sugerencias
    document.getElementById('suggestionsContainer').style.display = 'none';
    document.getElementById('noSuggestions').style.display = 'none';
    document.getElementById('loadingSuggestions').style.display = 'none';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botón siguiente
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (!validateCurrentStep()) return;
        
        if (currentStep === 2) {
            // Generar sugerencias
            generateSuggestions();
        }
        
        currentStep++;
        showStep(currentStep);
    });
    
    // Botón anterior
    document.getElementById('prevBtn').addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });
    
    // Botón confirmar
    document.getElementById('confirmBtn').addEventListener('click', confirmAppointment);
    
    // Event listener para cambio de tipo de consulta
    document.getElementById('ai_tipo_consulta').addEventListener('change', updateDuracionFromTipoConsulta);
    
    // Botones de duración
    document.getElementById('btnReducirDuracion').addEventListener('click', function() {
        if (currentDuracion > 15) {
            currentDuracion -= 30;
            updateDuracionDisplay();
        }
    });
    
    document.getElementById('btnAumentarDuracion').addEventListener('click', function() {
        if (currentDuracion < 180) {
            currentDuracion += 30;
            updateDuracionDisplay();
        }
    });
    
    // Resetear modal cuando se cierre
    document.getElementById('solicitudCitaModal2').addEventListener('hidden.bs.modal', resetModal);
    
    // Inicializar duración
    updateDuracionDisplay();
});
</script> 