<!-- Modal Crear Evento/Cita Médica -->
<div class="modal fade" id="crearEventoModal" tabindex="-1" aria-labelledby="crearEventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearEventoModalLabel">
                    <i class="ri-calendar-add-line me-2"></i>
                    Crear Nueva Cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Pestañas de navegación -->
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="crearCitaTabs" role="tablist">
                    @if(Auth::user()->hasRole('Facultativo'))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="presencial-tab" data-bs-toggle="tab" data-bs-target="#presencial" type="button" role="tab">
                                <i class="ri-user-heart-line me-1"></i> Consulta Presencial
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                                <i class="ri-edit-line me-1"></i> Creación Manual
                            </button>
                        </li>
                    @else
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                                <i class="ri-edit-line me-1"></i> Creación Manual
                            </button>
                        </li>
                    @endif
                </ul>

                <div class="tab-content" id="crearCitaTabContent">
                    <!-- Pestaña: Consulta Presencial (Solo para Facultativos) -->
                    @if(Auth::user()->hasRole('Facultativo'))
                    <div class="tab-pane fade show active" id="presencial" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <strong>Consulta Presencial:</strong> Complete los datos de la consulta que está realizando actualmente con el paciente.
                        </div>
                        
                        <form id="consultaPresencialForm">
                            <div class="row">
                                <!-- Datos del Paciente -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="ri-user-line me-2"></i>Datos del Paciente
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="paciente_presencial" class="form-label">Paciente *</label>
                                            <select class="form-select" id="paciente_presencial" name="paciente_id" required>
                                                <option value="">Seleccionar paciente</option>
                                                <!-- Los pacientes se cargarán dinámicamente -->
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="tipo_consulta" class="form-label">Tipo de Consulta *</label>
                                            <select class="form-select" id="tipo_consulta" name="tipo_consulta" required>
                                                <option value="">Seleccionar tipo</option>
                                                @foreach($motivosCita ?? [] as $motivo)
                                                    <option value="{{ $motivo->nombre }}">{{ $motivo->nombre }} ({{ $motivo->duracion_minutos }} min)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Motivo y Síntomas -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="ri-chat-1-line me-2"></i>Motivo de Consulta
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="motivo_consulta" class="form-label">Motivo principal *</label>
                                            <textarea class="form-control" id="motivo_consulta" name="motivo" rows="3" 
                                                placeholder="Describa el motivo principal de la consulta..." required></textarea>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="sintomas_presentes" class="form-label">Síntomas presentes</label>
                                            <textarea class="form-control" id="sintomas_presentes" name="sintomas" rows="3" 
                                                placeholder="Describa los síntomas que presenta el paciente..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evaluación Médica -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="ri-stethoscope-line me-2"></i>Evaluación Médica
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="especialidad_requerida" class="form-label">Especialidad requerida *</label>
                                            <select class="form-select" id="especialidad_requerida" name="especialidad_id" required>
                                                <option value="">Seleccionar especialidad</option>
                                                <option value="medicina_general">Medicina General</option>
                                                <option value="cardiologia">Cardiología</option>
                                                <option value="dermatologia">Dermatología</option>
                                                <option value="endocrinologia">Endocrinología</option>
                                                <option value="gastroenterologia">Gastroenterología</option>
                                                <option value="ginecologia">Ginecología</option>
                                                <option value="neurologia">Neurología</option>
                                                <option value="oftalmologia">Oftalmología</option>
                                                <option value="ortopedia">Ortopedia</option>
                                                <option value="pediatria">Pediatría</option>
                                                <option value="psiquiatria">Psiquiatría</option>
                                                <option value="traumatologia">Traumatología</option>
                                                <option value="urologia">Urología</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="urgencia_nivel" class="form-label">Nivel de urgencia</label>
                                            <select class="form-select" id="urgencia_nivel" name="urgencia">
                                                <option value="baja">Baja</option>
                                                <option value="media" selected>Media</option>
                                                <option value="alta">Alta</option>
                                                <option value="critica">Crítica</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="duracion_estimada" class="form-label">Duración estimada</label>
                                            <select class="form-select" id="duracion_estimada" name="duracion_minutos">
                                                <option value="15">15 minutos</option>
                                                <option value="30" selected>30 minutos</option>
                                                <option value="45">45 minutos</option>
                                                <option value="60">1 hora</option>
                                                <option value="90">1.5 horas</option>
                                                <option value="120">2 horas</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="necesita_analisis" class="form-label">¿Necesita análisis/estudios?</label>
                                            <select class="form-select" id="necesita_analisis" name="necesita_analisis">
                                                <option value="no">No</option>
                                                <option value="si">Sí</option>
                                                <option value="pendiente">Pendiente de evaluar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Programación -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="ri-calendar-line me-2"></i>Programación
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="fecha_consulta" class="form-label">Fecha de la consulta *</label>
                                            <input type="text" class="form-control datepicker" id="fecha_consulta" name="fecha_consulta" 
                                                   placeholder="dd/mm/yyyy" required readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="hora_consulta" class="form-label">Hora de la consulta *</label>
                                            <input type="time" class="form-control" id="hora_consulta" name="hora_consulta" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="fecha_seguimiento" class="form-label">Fecha de seguimiento (opcional)</label>
                                            <input type="text" class="form-control datepicker" id="fecha_seguimiento" name="fecha_seguimiento" 
                                                   placeholder="dd/mm/yyyy" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="recordatorio" class="form-label">Recordatorio</label>
                                            <select class="form-select" id="recordatorio" name="recordatorio">
                                                <option value="no">Sin recordatorio</option>
                                                <option value="1_dia">1 día antes</option>
                                                <option value="2_dias" selected>2 días antes</option>
                                                <option value="1_semana">1 semana antes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observaciones Adicionales -->
                                <div class="col-md-12 mb-4">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="ri-file-text-line me-2"></i>Observaciones Adicionales
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="observaciones_medicas" class="form-label">Observaciones médicas</label>
                                            <textarea class="form-control" id="observaciones_medicas" name="observaciones" rows="3" 
                                                placeholder="Observaciones adicionales, recomendaciones, etc..."></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="medicamentos_recetados" class="form-label">Medicamentos recetados</label>
                                            <textarea class="form-control" id="medicamentos_recetados" name="medicamentos" rows="2" 
                                                placeholder="Lista de medicamentos recetados..."></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="indicaciones" class="form-label">Indicaciones para el paciente</label>
                                            <textarea class="form-control" id="indicaciones" name="indicaciones" rows="2" 
                                                placeholder="Indicaciones específicas para el paciente..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @endif

                    <!-- Pestaña: Creación Manual -->
                    <div class="tab-pane fade @if(!Auth::user()->hasRole('Facultativo')) show active @endif" id="manual" role="tabpanel">
                        <form id="crearEventoForm">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="titulo" class="form-label">Título de la cita *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha de la cita *</label>
                                    <input type="text" class="form-control datepicker" id="fecha_inicio" name="fecha_inicio" 
                                           placeholder="dd/mm/yyyy" required readonly>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="hora_inicio" class="form-label">Hora de la cita *</label>
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="duracion" class="form-label">Duración de la cita *</label>
                                    <select class="form-select" id="duracion" name="duracion" required>
                                        <option value="">Seleccionar duración</option>
                                        <option value="15">15 minutos</option>
                                        <option value="30" selected>30 minutos</option>
                                        <option value="45">45 minutos</option>
                                        <option value="60">1 hora</option>
                                        <option value="90">1.5 horas</option>
                                        <option value="120">2 horas</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_cita" class="form-label">Tipo de cita</label>
                                    <select class="form-select" id="tipo_cita" name="tipo_cita">
                                        <option value="consulta">Consulta general</option>
                                        <option value="especialidad">Especialidad</option>
                                        <option value="urgencia">Urgencia</option>
                                        <option value="seguimiento">Seguimiento</option>
                                        <option value="revision">Revisión</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Detalles de la cita médica..."></textarea>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="programada">Programada</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="cancelada">Cancelada</option>
                                        <option value="completada">Completada</option>
                                    </select>
                                </div>
                                
                                @if(Auth::user()->hasRole('Facultativo') || Auth::user()->hasRole('Administrador'))
                                <div class="col-md-6 mb-3">
                                    <label for="paciente_id" class="form-label">Paciente</label>
                                    <select class="form-select" id="paciente_id" name="paciente_id">
                                        <option value="">Seleccionar paciente</option>
                                        <!-- Los pacientes se cargarán dinámicamente -->
                                    </select>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Cancelar
                </button>
                
                <!-- Botones para pestaña presencial -->
                @if(Auth::user()->hasRole('Facultativo'))
                <div class="tab-pane-buttons" id="presencial-buttons">
                    <button type="submit" form="consultaPresencialForm" class="btn btn-success">
                        <i class="ri-save-line me-1"></i> Registrar Consulta
                    </button>
                </div>
                @endif
                
                <!-- Botones para pestaña manual -->
                <div class="tab-pane-buttons @if(!Auth::user()->hasRole('Facultativo')) d-block @else d-none @endif" id="manual-buttons">
                    <button type="submit" form="crearEventoForm" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i> Crear Cita
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Funcionalidad para las pestañas
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Flatpickr para datepickers
    flatpickr(".datepicker", {
        dateFormat: "d/m/Y",
        locale: "es",
        allowInput: true,
        clickOpens: true,
        todayHighlight: true,
        firstDayOfWeek: 1, // Lunes
        onChange: function(selectedDates, dateStr, instance) {
            // Convertir formato dd/mm/yyyy a yyyy-mm-dd para el backend
            if (selectedDates[0]) {
                const date = selectedDates[0];
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                const backendFormat = `${yyyy}-${mm}-${dd}`;
                
                // Crear un campo oculto con el formato del backend
                let hiddenField = instance.input.parentNode.querySelector('input[name="' + instance.input.name + '_backend"]');
                if (!hiddenField) {
                    hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = instance.input.name + '_backend';
                    instance.input.parentNode.appendChild(hiddenField);
                }
                hiddenField.value = backendFormat;
            }
        }
    });

    // Manejar cambios de pestaña para mostrar/ocultar botones
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const targetId = event.target.getAttribute('data-bs-target').substring(1);
            
            // Ocultar todos los botones
            document.querySelectorAll('.tab-pane-buttons').forEach(btn => {
                btn.classList.add('d-none');
            });
            
            // Mostrar los botones correspondientes
            const buttons = document.getElementById(targetId + '-buttons');
            if (buttons) {
                buttons.classList.remove('d-none');
            }
        });
    });

    // Calcular automáticamente la fecha de fin basada en la duración
    document.getElementById('duracion')?.addEventListener('change', function() {
        const duracion = parseInt(this.value);
        const fechaInput = document.getElementById('fecha_inicio');
        const horaInput = document.getElementById('hora_inicio');
        
        if (duracion && fechaInput.value && horaInput.value) {
            // Crear un campo oculto con la fecha de fin calculada
            let hiddenField = document.querySelector('input[name="fecha_fin"]');
            if (!hiddenField) {
                hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = 'fecha_fin';
                document.getElementById('crearEventoForm').appendChild(hiddenField);
            }
            
            // Calcular fecha de fin
            const fechaHora = fechaInput.value + ' ' + horaInput.value;
            const fechaInicio = new Date(fechaHora);
            const fechaFin = new Date(fechaInicio.getTime() + (duracion * 60000));
            
            hiddenField.value = fechaFin.toISOString().slice(0, 16);
        }
    });

    // Cargar pacientes en ambos selects al abrir el modal
    const cargarPacientes = async () => {
        try {
            const response = await fetch('/facultativo/calendario/pacientes');
            const pacientes = await response.json();
            const selects = [document.getElementById('paciente_presencial'), document.getElementById('paciente_id')];
            selects.forEach(select => {
                if (!select) return;
                select.innerHTML = '<option value="">Seleccionar paciente</option>';
                pacientes.forEach(paciente => {
                    const option = document.createElement('option');
                    option.value = paciente.id;
                    option.textContent = `${paciente.name} (${paciente.email})`;
                    select.appendChild(option);
                });
            });
        } catch (error) {
            console.error('Error cargando pacientes:', error);
        }
    };
    // Cuando se abre el modal, cargar pacientes
    const modal = document.getElementById('crearEventoModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', cargarPacientes);
    }
});
</script> 