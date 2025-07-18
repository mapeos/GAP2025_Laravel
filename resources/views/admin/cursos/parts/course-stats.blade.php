{{-- 
    PARTIAL: Course Stats (Estadísticas del Curso)
    ==============================================
    
    FUNCIÓN: Muestra métricas visuales y estadísticas del curso
    - Tarjetas de métricas: Inscritos, plazas disponibles, ocupación, días para iniciar
    - Barra de progreso: Visualización de la ocupación con colores dinámicos
    - Indicadores visuales: Iconos y colores según el estado de ocupación
    
    VARIABLES REQUERIDAS:
    - $curso: Modelo Curso con relaciones cargadas
    
    MÉTODOS UTILIZADOS:
    - $curso->getInscritosCount()
    - $curso->getPlazasDisponibles()
    - $curso->getPorcentajeOcupacion()
    
    USO: @include('admin.cursos.parts.course-stats', ['curso' => $curso])
--}}

{{-- Tarjetas de métricas principales --}}
<div class="row mb-4">
    {{-- Tarjeta: Total de inscritos --}}
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="display-6 text-primary mb-2">
                    <i class="ri-user-line"></i>
                </div>
                <h4 class="text-primary">{{ $curso->getInscritosCount() }}</h4>
                <p class="text-muted mb-0">Inscritos</p>
            </div>
        </div>
    </div>
    
    {{-- Tarjeta: Plazas disponibles --}}
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="display-6 text-success mb-2">
                    <i class="ri-seat-line"></i>
                </div>
                <h4 class="text-success">{{ $curso->getPlazasDisponibles() }}</h4>
                <p class="text-muted mb-0">Plazas disponibles</p>
            </div>
        </div>
    </div>
    
    {{-- Tarjeta: Porcentaje de ocupación --}}
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="display-6 text-info mb-2">
                    <i class="ri-percent-line"></i>
                </div>
                <h4 class="text-info">{{ number_format($curso->getPorcentajeOcupacion(), 1) }}%</h4>
                <p class="text-muted mb-0">Ocupación</p>
            </div>
        </div>
    </div>
    
    {{-- Tarjeta: Estado temporal del curso --}}
    <div class="col-md-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                @php
                    $fechaInicio = \Carbon\Carbon::parse($curso->fechaInicio)->startOfDay();
                    $fechaFin = \Carbon\Carbon::parse($curso->fechaFin)->startOfDay();
                    $hoy = now()->startOfDay();
                    
                    if ($hoy < $fechaInicio) {
                        // Curso futuro - días para iniciar
                        $dias = $hoy->diffInDays($fechaInicio, false); // false para evitar números negativos
                        $iconClass = 'ri-calendar-check-line';
                        $textClass = 'text-warning';
                        $texto = 'Días para iniciar';
                        
                        // Formatear número grande
                        if ($dias > 999) {
                            $diasFormateado = number_format($dias);
                        } else {
                            $diasFormateado = $dias;
                        }
                        
                    } elseif ($hoy >= $fechaInicio && $hoy <= $fechaFin) {
                        // Curso en curso - días restantes
                        $dias = $hoy->diffInDays($fechaFin, false); // false para evitar números negativos
                        $iconClass = 'ri-time-line';
                        $textClass = 'text-success';
                        
                        // Casos especiales para cursos en progreso
                        if ($dias == 0) {
                            // Último día del curso
                            $texto = 'Finaliza hoy';
                            $diasFormateado = '¡Último día!';
                        } elseif ($dias == 1) {
                            // Penúltimo día
                            $texto = 'Finaliza mañana';
                            $diasFormateado = '1 día';
                        } elseif ($hoy->equalTo($fechaInicio)) {
                            // Primer día del curso
                            $texto = 'Comenzó hoy';
                            $diasFormateado = '¡En curso!';
                        } else {
                            // Días normales restantes
                            $texto = 'Días restantes';
                            // Formatear número grande
                            if ($dias > 999) {
                                $diasFormateado = number_format($dias);
                            } else {
                                $diasFormateado = $dias;
                            }
                        }
                        
                    } else {
                        // Curso finalizado
                        $dias = $fechaFin->diffInDays($hoy, false); // false para evitar números negativos
                        $iconClass = 'ri-check-double-line';
                        $textClass = 'text-secondary';
                        $texto = 'Finalizado hace';
                        
                        // Formatear número grande
                        if ($dias > 999) {
                            $diasFormateado = number_format($dias);
                        } else {
                            $diasFormateado = $dias;
                        }
                    }
                    
                    // Asegurar que nunca sea negativo
                    if ($dias < 0) {
                        $dias = 0;
                        $diasFormateado = '0';
                    }
                @endphp
                
                <div class="display-6 {{ $textClass }} mb-2">
                    <i class="{{ $iconClass }}"></i>
                </div>
                <h4 class="{{ $textClass }}">{{ $diasFormateado }}</h4>
                <p class="text-muted mb-0">{{ $texto }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Barra de progreso de ocupación --}}
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="ri-bar-chart-line me-2"></i>
            Progreso de Ocupación
        </h6>
    </div>
    <div class="card-body">
        {{-- Información de la barra --}}
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Inscritos: {{ $curso->getInscritosCount() }}</span>
            <span class="text-muted">Total: {{ $curso->plazas }}</span>
        </div>
        
        {{-- Barra de progreso --}}
        <div class="progress" style="height: 25px;">
            @php
                $porcentaje = $curso->getPorcentajeOcupacion();
                $colorClass = $porcentaje >= 90 ? 'bg-danger' : ($porcentaje >= 50 ? 'bg-warning' : 'bg-success');
            @endphp
            <div class="progress-bar {{ $colorClass }}" 
                 role="progressbar" 
                 style="width: {{ $porcentaje }}%"
                 aria-valuenow="{{ $porcentaje }}"
                 aria-valuemin="0" 
                 aria-valuemax="100">
                {{ number_format($porcentaje, 1) }}%
            </div>
        </div>
        
        {{-- Mensaje de estado --}}
        <small class="text-muted mt-2 d-block">
            @if($porcentaje >= 90)
                <i class="ri-alert-line text-danger me-1"></i> ¡Casi completo!
            @elseif($porcentaje >= 50)
                <i class="ri-information-line text-warning me-1"></i> Ocupación media
            @else
                <i class="ri-check-line text-success me-1"></i> Muchas plazas disponibles
            @endif
        </small>
    </div>
</div> 