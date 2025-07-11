<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th width="25%">Alumno</th>
                <th width="35%">Curso</th>
                <th width="10%">Precio</th>
                <th width="15%">Estado</th>
                <th width="15%">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($solicitudes as $solicitud)
                <tr>
                    <td>
                        @if(isset($solicitud->pivot->persona) && $solicitud->pivot->persona)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm bg-primary-subtle rounded-circle me-2 d-flex align-items-center justify-content-center">
                                    <i class="ri-user-line text-primary"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.participantes.show', $solicitud->pivot->persona->id) }}" 
                                       class="text-decoration-none fw-semibold">
                                        {{ $solicitud->pivot->persona->getNombreCompletoAttribute() }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <i class="ri-mail-line me-1"></i>
                                        {{ $solicitud->pivot->persona->user->email ?? 'Sin email' }}
                                    </small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Sin datos</span>
                        @endif
                    </td>
                    <td>
                        @if(isset($solicitud->curso) && $solicitud->curso)
                            <div>
                                <strong class="text-primary">{{ $solicitud->curso->titulo }}</strong>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="ri-calendar-line me-1"></i>
                                        {{ $solicitud->curso->fechaInicio->format('d/m/Y') }} - {{ $solicitud->curso->fechaFin->format('d/m/Y') }}
                                    </small>
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        <i class="ri-group-line me-1"></i>
                                        {{ $solicitud->curso->getInscritosCount() }}/{{ $solicitud->curso->plazas }} plazas
                                    </small>
                                </div>
                                @if($solicitud->curso->descripcion)
                                <div class="mt-1">
                                    <small class="text-muted">
                                        {{ Str::limit($solicitud->curso->descripcion, 60) }}
                                    </small>
                                </div>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">Sin datos</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(isset($solicitud->curso) && $solicitud->curso)
                            @if($solicitud->curso->precio)
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-money-euro-circle-line me-1"></i>
                                    {{ number_format($solicitud->curso->precio, 2) }} €
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info">
                                    <i class="ri-gift-line me-1"></i>
                                    Gratuito
                                </span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($solicitud->pivot->estado == 'activo')
                            <span class="badge bg-success">
                                <i class="ri-check-line me-1"></i> Aceptado
                            </span>
                        @elseif($solicitud->pivot->estado == 'pendiente')
                            <span class="badge bg-warning text-dark">
                                <i class="ri-time-line me-1"></i> Pendiente
                            </span>
                        @elseif($solicitud->pivot->estado == 'rechazado')
                            <span class="badge bg-danger">
                                <i class="ri-close-line me-1"></i> Rechazado
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="ri-question-line me-1"></i> Desconocido
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(isset($solicitud->curso) && $solicitud->curso && isset($solicitud->pivot->persona) && $solicitud->pivot->persona)
                            <!-- Botón Ver Detalles -->
                            <a href="{{ route('admin.solicitudes.show', [$solicitud->curso->id, $solicitud->pivot->persona->id]) }}" 
                               class="btn btn-outline-info btn-sm w-100 mb-1" title="Ver detalles completos">
                                <i class="ri-eye-line me-1"></i> Ver detalles
                            </a>
                            
                            <!-- Botones de Acción según Estado -->
                            @if($solicitud->pivot->estado == 'pendiente')
                                <!-- Aceptar -->
                                <form method="POST" action="{{ route('admin.solicitudes.update', [$solicitud->curso->id, $solicitud->pivot->persona->id]) }}" class="mb-1">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="estado" value="activo">
                                    <button type="submit" class="btn btn-success btn-sm w-100 action-btn" 
                                            data-action="aceptar"
                                            onclick="return confirm('¿Estás seguro de que quieres aceptar esta solicitud?')">
                                        <i class="ri-check-line me-1"></i> Aceptar
                                    </button>
                                </form>
                                
                                <!-- Rechazar -->
                                <form method="POST" action="{{ route('admin.solicitudes.update', [$solicitud->curso->id, $solicitud->pivot->persona->id]) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="estado" value="rechazado">
                                    <button type="submit" class="btn btn-danger btn-sm w-100 action-btn" 
                                            data-action="rechazar"
                                            onclick="return confirm('¿Estás seguro de que quieres rechazar esta solicitud?')">
                                        <i class="ri-close-line me-1"></i> Rechazar
                                    </button>
                                </form>
                                
                            @elseif($solicitud->pivot->estado == 'rechazado')
                                <!-- Reactivar -->
                                <form method="POST" action="{{ route('admin.solicitudes.update', [$solicitud->curso->id, $solicitud->pivot->persona->id]) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="estado" value="activo">
                                    <button type="submit" class="btn btn-success btn-sm w-100 action-btn" 
                                            data-action="reactivar"
                                            onclick="return confirm('¿Estás seguro de que quieres reactivar esta solicitud rechazada?')">
                                        <i class="ri-refresh-line me-1"></i> Reactivar
                                    </button>
                                </form>
                                
                            @elseif($solicitud->pivot->estado == 'activo')
                                <!-- Ya aceptado -->
                                <div class="text-center">
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-check-double-line me-1"></i> Aceptado
                                    </span>
                                </div>
                            @endif
                        @else
                            <span class="text-muted">No disponible</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="text-muted">
                            <i class="ri-inbox-line fs-1 mb-3"></i>
                            <h5>No hay solicitudes de inscripción</h5>
                            <p class="mb-0">No se encontraron solicitudes con los filtros aplicados.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginación -->
@if($solicitudes->hasPages())
<div class="d-flex justify-content-center mt-3 admin-solicitudes-pagination">
    {{ $solicitudes->links() }}
</div>
@endif

<!-- Modal de Loading -->
<div id="loadingModal" class="modal fade" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-5">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h5 class="text-primary mb-2">Procesando solicitud</h5>
                <p class="text-muted mb-0" id="loadingMessage">Actualizando estado de la solicitud...</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para el modal de loading */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.7) !important;
}

.modal-content {
    background: white;
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.spinner-border {
    border-width: 0.25em;
}

/* Animación personalizada para el spinner */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spinner-border {
    animation: spin 1s linear infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener elementos
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    const loadingMessage = document.getElementById('loadingMessage');
    
    // Mapeo de mensajes por acción
    const actionMessages = {
        'aceptar': 'Aceptando solicitud...',
        'rechazar': 'Rechazando solicitud...',
        'reactivar': 'Reactivando solicitud...'
    };
    
    // Agregar event listeners a todos los botones de acción
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            // Obtener el tipo de acción
            const action = this.getAttribute('data-action');
            const message = actionMessages[action] || 'Procesando solicitud...';
            
            // Actualizar mensaje
            loadingMessage.textContent = message;
            
            // Mostrar modal de loading
            loadingModal.show();
            
            // El formulario se enviará automáticamente después de la confirmación
        });
    });
    
    // Ocultar modal cuando la página se recarga (después de una acción exitosa)
    if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_RELOAD) {
        // La página se recargó, no mostrar loading
        return;
    }
});
</script>
