<!-- Modal Solicitud Cita -->
<div class="modal fade" id="solicitudCitaModal" tabindex="-1" aria-labelledby="solicitudCitaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="solicitudCitaModalLabel">Solicitar Cita/Consulta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="solicitudCitaForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profesor_id" class="form-label">Profesor *</label>
                                <select class="form-select" id="profesor_id" name="profesor_id" required>
                                    <option value="">Seleccionar profesor</option>
                                    @foreach($profesores ?? [] as $profesor)
                                        <option value="{{ $profesor->id }}">{{ $profesor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_propuesta" class="form-label">Fecha Propuesta *</label>
                                <input type="datetime-local" class="form-control" id="fecha_propuesta" name="fecha_propuesta" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de la consulta *</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required 
                                  placeholder="Describe el motivo de tu consulta..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_consulta" class="form-label">Tipo de Consulta</label>
                        <select class="form-select" id="tipo_consulta" name="tipo_consulta">
                            <option value="academica">Académica</option>
                            <option value="personal">Personal</option>
                            <option value="trabajo">Trabajo</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones adicionales</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"
                                  placeholder="Información adicional que consideres relevante..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div> 