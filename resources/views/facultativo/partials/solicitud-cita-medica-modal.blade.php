<!-- Modal Solicitud Cita Médica -->
<div class="modal fade" id="solicitudCitaMedicaModal" tabindex="-1" aria-labelledby="solicitudCitaMedicaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="solicitudCitaMedicaModalLabel">
                    <i class="ri-heart-pulse-line me-2"></i>Solicitar Cita Médica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="solicitudCitaMedicaForm" action="#" method="POST">
                @csrf
                <input type="hidden" name="tipo_sistema" value="medico">
                <div class="modal-body">
                    <!-- Información del Paciente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-success border-bottom pb-2">
                                <i class="ri-user-line me-2"></i>Información del Paciente
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="alumno_id" class="form-label">Paciente *</label>
                                <select class="form-select" id="alumno_id" name="alumno_id" required>
                                    <option value="">Seleccionar paciente</option>
                                    @foreach($pacientes ?? [] as $paciente)
                                        <option value="{{ $paciente->id }}">{{ $paciente->name }} - {{ $paciente->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="facultativo_id" class="form-label">Médico *</label>
                                <select class="form-select" id="facultativo_id" name="facultativo_id" required>
                                    <option value="">Seleccionar médico</option>
                                    @foreach($facultativos ?? [] as $facultativo)
                                        <option value="{{ $facultativo->id }}">
                                            Dr. {{ $facultativo->user->name }} - {{ $facultativo->especialidad->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Cita -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-success border-bottom pb-2">
                                <i class="ri-calendar-line me-2"></i>Información de la Cita
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_propuesta" class="form-label">Fecha y Hora *</label>
                                <input type="datetime-local" class="form-control" id="fecha_propuesta" name="fecha_propuesta" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duracion_minutos" class="form-label">Duración (minutos)</label>
                                <select class="form-select" id="duracion_minutos" name="duracion_minutos">
                                    <option value="30">30 minutos</option>
                                    <option value="45" selected>45 minutos</option>
                                    <option value="60">1 hora</option>
                                    <option value="90">1.5 horas</option>
                                    <option value="120">2 horas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Información Médica -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-success border-bottom pb-2">
                                <i class="ri-heart-pulse-line me-2"></i>Información Médica
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="especialidad_id" class="form-label">Especialidad</label>
                                <select class="form-select" id="especialidad_id" name="especialidad_id">
                                    <option value="">Seleccionar especialidad</option>
                                    @foreach($especialidades ?? [] as $especialidad)
                                        <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tratamiento_id" class="form-label">Tratamiento</label>
                                <select class="form-select" id="tratamiento_id" name="tratamiento_id">
                                    <option value="">Seleccionar tratamiento</option>
                                    @foreach($tratamientos ?? [] as $tratamiento)
                                        <option value="{{ $tratamiento->id }}" data-costo="{{ $tratamiento->costo }}">
                                            {{ $tratamiento->nombre }} ({{ $tratamiento->costo_formateado }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="motivo" class="form-label">Motivo de la consulta *</label>
                                <textarea class="form-control" id="motivo" name="motivo" rows="3" required 
                                          placeholder="Describe el motivo principal de la consulta..."></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="sintomas" class="form-label">Síntomas</label>
                                <textarea class="form-control" id="sintomas" name="sintomas" rows="3"
                                          placeholder="Describe los síntomas que presenta el paciente..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-success border-bottom pb-2">
                                <i class="ri-information-line me-2"></i>Información Adicional
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="costo" class="form-label">Costo estimado (€)</label>
                                <input type="number" class="form-control" id="costo" name="costo" step="0.01" min="0"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prioridad" class="form-label">Prioridad</label>
                                <select class="form-select" id="prioridad" name="prioridad">
                                    <option value="normal" selected>Normal</option>
                                    <option value="urgente">Urgente</option>
                                    <option value="muy_urgente">Muy Urgente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="observaciones_medicas" class="form-label">Observaciones médicas</label>
                                <textarea class="form-control" id="observaciones_medicas" name="observaciones_medicas" rows="3"
                                          placeholder="Observaciones adicionales relevantes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-send-plane-line me-1"></i>Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar costo cuando se selecciona un tratamiento
    document.getElementById('tratamiento_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const costo = selectedOption.getAttribute('data-costo');
        if (costo) {
            document.getElementById('costo').value = costo;
        }
    });

    // Validación del formulario
    document.getElementById('solicitudCitaMedicaForm').addEventListener('submit', function(e) {
        const fecha = document.getElementById('fecha_propuesta').value;
        const ahora = new Date();
        const fechaSeleccionada = new Date(fecha);
        
        if (fechaSeleccionada <= ahora) {
            e.preventDefault();
            alert('La fecha y hora de la cita debe ser posterior a la fecha actual.');
            return false;
        }
    });
});
</script> 