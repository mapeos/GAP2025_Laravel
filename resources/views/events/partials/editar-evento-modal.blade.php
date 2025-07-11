<!-- Modal Editar Evento -->
<div class="modal fade" id="editarEventoModal" tabindex="-1" aria-labelledby="editarEventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarEventoModalLabel">
                    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
                        Editar Evento
                    @else
                        Detalles del Evento
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- version de edicion para profesores y Administradores -->
            @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
            <form id="editarEventoForm" onsubmit="handleEditarEvento(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="editEventoId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="editTitulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="editTitulo" name="titulo" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="editTipoEventoId" class="form-label">Tipo de Evento *</label>
                                <select class="form-select" id="editTipoEventoId" name="tipo_evento_id" required>
                                    <option value="">Seleccionar tipo</option>
                                    @foreach(\App\Models\TipoEvento::where('status', true)->get() as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFechaInicio" class="form-label">Fecha de inicio *</label>
                                <input type="datetime-local" class="form-control" id="editFechaInicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editFechaFin" class="form-label">Fecha de fin *</label>
                                <input type="datetime-local" class="form-control" id="editFechaFin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"
                                  placeholder="Descripción del evento..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUbicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="editUbicacion" name="ubicacion"
                                       placeholder="Ubicación física del evento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editUrlVirtual" class="form-label">URL Virtual</label>
                                <input type="url" class="form-control" id="editUrlVirtual" name="url_virtual"
                                       placeholder="https://meet.google.com/...">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Estado</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editParticipantes" class="form-label">Participantes</label>
                        <select class="form-select" id="editParticipantes" name="participantes[]" multiple>
                            @foreach(\App\Models\User::where('status', 'activo')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples participantes</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <button type="button" class="btn btn-danger" onclick="deleteEvento(event)">
                            <i class="ri-delete-bin-line"></i> Eliminar
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Evento</button>
                </div>
            </form>
            @else
            <!-- Versión de solo lectura para estudiantes -->
            <div class="modal-body">
                <input type="hidden" id="editEventoId">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Título</label>
                            <p id="viewTitulo" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Evento</label>
                            <p id="viewTipoEvento" class="form-control-plaintext"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de inicio</label>
                            <p id="viewFechaInicio" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de fin</label>
                            <p id="viewFechaFin" class="form-control-plaintext"></p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción</label>
                    <p id="viewDescripcion" class="form-control-plaintext"></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ubicación</label>
                            <p id="viewUbicacion" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">URL Virtual</label>
                            <p id="viewUrlVirtual" class="form-control-plaintext"></p>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Estado</label>
                    <p id="viewStatus" class="form-control-plaintext"></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Participantes</label>
                    <p id="viewParticipantes" class="form-control-plaintext"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
            @endif
        </div>
    </div>
</div>
