{{-- 
    PARTIAL: Status Alerts (Alertas de Estado)
    =========================================
    
    FUNCIÓN: Muestra alertas visuales del estado actual del curso
    - Cursos eliminados (soft delete): Alerta amarilla con fecha de eliminación
    - Cursos activos: Alerta verde confirmando disponibilidad
    - Cursos inactivos: Alerta gris indicando no disponibilidad
    
    VARIABLES REQUERIDAS:
    - $curso: Modelo Curso con relaciones cargadas
    
    USO: @include('admin.cursos.parts.status-alerts', ['curso' => $curso])
--}}

{{-- Alertas de estado del curso --}}
@if ($curso->trashed())
<div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="ri-close-circle-fill text-danger me-2 fs-4"></i>
    <div>
        Este curso ha sido <strong>eliminado</strong>.
        @if($curso->deleted_at)
            <br><small class="text-muted">Eliminado el: {{ $curso->deleted_at->format('d/m/Y H:i') }}</small>
        @endif
    </div>
</div>
@elseif($curso->estado === 'activo')
<div class="alert alert-success d-flex align-items-center" role="alert">
    <i class="ri-checkbox-circle-fill text-success me-2 fs-4"></i>
    <div>
        Este curso está <strong>activo</strong> y disponible para inscripciones.
    </div>
</div>
@else
<div class="alert alert-secondary d-flex align-items-center" role="alert">
    <i class="ri-information-line text-secondary me-2 fs-4"></i>
    <div>
        Este curso está <strong>inactivo</strong> y no disponible para inscripciones.
    </div>
</div>
@endif 