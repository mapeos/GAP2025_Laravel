<!-- Modal Crear Evento -->
<div class="modal fade" id="crearEventoModal" tabindex="-1" aria-labelledby="crearEventoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearEventoModalLabel">
                    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
                        Crear Nuevo Evento
                    @else
                        Crear Recordatorio Personal
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="crearEventoForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                        </div>
                        @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tipo_evento_id" class="form-label">Tipo de Evento *</label>
                                <select class="form-select" id="tipo_evento_id" name="tipo_evento_id" required>
                                    <option value="">Seleccionar tipo</option>
                                    @foreach(\App\Models\TipoEvento::where('status', true)->get() as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de inicio *</label>
                                <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de fin *</label>
                                <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                  placeholder="Descripción del evento..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion"
                                       placeholder="Ubicación física del evento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="url_virtual" class="form-label">URL Virtual</label>
                                <input type="url" class="form-control" id="url_virtual" name="url_virtual"
                                       placeholder="https://meet.google.com/...">
                            </div>
                        </div>
                    </div>
                    
                    @if(Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Profesor'))
                    <div class="mb-3">
                        <label for="participantes" class="form-label">Participantes</label>
                        <select class="form-select" id="participantes" name="participantes[]" multiple>
                            @foreach(\App\Models\User::where('status', 'activo')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Mantén presionado Ctrl (Cmd en Mac) para seleccionar múltiples participantes</small>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Evento</button>
                </div>
            </form>
        </div>
    </div>
</div> 