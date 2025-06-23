{{-- 
    PARTIAL: Course Details (Detalles del Curso)
    ============================================
    
    FUNCIÓN: Muestra toda la información detallada del curso en formato tabla
    - Información básica: ID, título, descripción, fechas
    - Métricas: Plazas disponibles, porcentaje de ocupación
    - Estado: Badge visual del estado (activo/inactivo)
    - Imagen de portada: Vista previa si existe
    - Botones de acción: Editar y volver al listado
    
    VARIABLES REQUERIDAS:
    - $curso: Modelo Curso con relaciones cargadas
    
    MÉTODOS UTILIZADOS:
    - $curso->getPlazasDisponibles()
    - $curso->getPorcentajeOcupacion()
    - $curso->getPlazasColorClass()
    
    USO: @include('admin.cursos.parts.course-details', ['curso' => $curso])
--}}

{{-- Detalles completos del curso --}}
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="ri-file-list-line me-2"></i>
            Detalles del Curso
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cursos.edit', $curso->id) }}" 
               class="btn btn-warning btn-sm" 
               title="Editar curso">
                <i class="ri-edit-line me-1"></i> Editar
            </a>
            <a href="{{ route('admin.cursos.index') }}" 
               class="btn btn-secondary btn-sm" 
               title="Volver al listado">
                <i class="ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="150" class="text-muted">ID:</th>
                                <td><strong>{{ $curso->id }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Título:</th>
                                <td><strong>{{ $curso->titulo }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Descripción:</th>
                                <td>{{ $curso->descripcion ?: 'Sin descripción' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Fechas:</th>
                                <td>
                                    <i class="ri-calendar-line me-1"></i>
                                    {{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Plazas:</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="{{ $curso->getPlazasColorClass() }} me-2">
                                            <strong>{{ $curso->getPlazasDisponibles() }}</strong> / {{ $curso->plazas }}
                                        </div>
                                        <small class="text-muted">
                                            {{ number_format($curso->getPorcentajeOcupacion(), 1) }}% ocupado
                                        </small>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Estado:</th>
                                <td>
                                    @if($curso->estado === 'activo')
                                        <span class="badge bg-success">
                                            <i class="ri-check-line me-1"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="ri-close-line me-1"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if($curso->precio)
                            <tr>
                                <th class="text-muted">Precio:</th>
                                <td>
                                    <i class="ri-money-euro-circle-line me-1"></i>
                                    {{ number_format($curso->precio, 2) }} €
                                </td>
                            </tr>
                            @endif
                            @if($curso->trashed())
                            <tr>
                                <th class="text-muted">Eliminado:</th>
                                <td>
                                    <span class="text-danger">
                                        <i class="ri-delete-bin-line me-1"></i>
                                        {{ $curso->deleted_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                @if ($curso->portada_path)
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $curso->portada_path) }}" 
                             alt="Portada del curso" 
                             class="img-fluid rounded shadow-sm" 
                             style="max-width: 100%; max-height: 200px;">
                        <small class="text-muted d-block mt-2">Imagen de portada</small>
                    </div>
                @else
                    <div class="text-center text-muted">
                        <i class="ri-image-line fs-1"></i>
                        <p class="mt-2">Sin imagen de portada</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 