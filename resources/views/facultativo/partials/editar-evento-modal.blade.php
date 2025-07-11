<!-- Modal Editar Evento -->
<div class="modal fade" id="editarEventoModal" tabindex="-1" aria-labelledby="editarEventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarEventoModalLabel">
                    <i class="ri-edit-line me-2"></i>
                    Editar Cita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editarEventoForm">
                <input type="hidden" id="editEventoId" name="evento_id">
                <div class="modal-body">
                    <!-- Campos editables para facultativos y administradores -->
                    <div class="edit-field">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="editTitulo" class="form-label">Título de la cita *</label>
                                <input type="text" class="form-control" id="editTitulo" name="titulo" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="editFechaInicio" class="form-label">Fecha y hora de inicio *</label>
                                <input type="datetime-local" class="form-control" id="editFechaInicio" name="fecha_inicio" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="editFechaFin" class="form-label">Fecha y hora de fin *</label>
                                <input type="datetime-local" class="form-control" id="editFechaFin" name="fecha_fin" required>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="editDescripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="editTipoCita" class="form-label">Tipo de cita</label>
                                <select class="form-select" id="editTipoCita" name="tipo_cita">
                                    <option value="consulta">Consulta general</option>
                                    <option value="especialidad">Especialidad</option>
                                    <option value="urgencia">Urgencia</option>
                                    <option value="seguimiento">Seguimiento</option>
                                    <option value="revision">Revisión</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="editEstado" class="form-label">Estado</label>
                                <select class="form-select" id="editEstado" name="estado">
                                    <option value="programada">Programada</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="cancelada">Cancelada</option>
                                    <option value="completada">Completada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos de solo lectura para pacientes -->
                    <div class="view-field" style="display: none;">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Título de la cita</label>
                                <p class="form-control-plaintext" id="viewTitulo"></p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha y hora de inicio</label>
                                <p class="form-control-plaintext" id="viewFechaInicio"></p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha y hora de fin</label>
                                <p class="form-control-plaintext" id="viewFechaFin"></p>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Descripción</label>
                                <p class="form-control-plaintext" id="viewDescripcion"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cerrar
                    </button>
                    <div class="edit-field">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> 