{{-- 
    PARTIAL: Participante (Gestión de Participantes)
    ===============================================
    
    FUNCIÓN: Gestiona y muestra la lista de participantes del curso
    - Lista de participantes inscritos con información detallada
    - Tabla responsive con nombre, email y rol
    - Botón para añadir nuevos participantes
    - Estado vacío con llamada a la acción
    - Enlace para ver todos los inscritos
    
    VARIABLES REQUERIDAS:
    - $curso: Modelo Curso con relaciones 'personas' cargadas
    
    RELACIONES UTILIZADAS:
    - $curso->personas (relación many-to-many con Persona)
    - $persona->user (relación con User para email)
    - $persona->pivot->rol_participacion_id (rol del participante)
    
    USO: @include('admin.cursos.parts.participante', ['curso' => $curso])
--}}

@php
    // Verificar que la variable $curso existe y tiene el ID
    if (!isset($curso) || !$curso || !$curso->id) {
        echo '<div class="alert alert-danger">Error: No se proporcionó un curso válido al partial de participantes.</div>';
        return;
    }
    
    // Cargar las relaciones si no están cargadas
    if (!$curso->relationLoaded('personas')) {
        $curso->load('personas.user');
    }
    
    $totalParticipantes = $curso->personas ? $curso->personas->count() : 0;
@endphp

{{-- Gestión de participantes del curso --}}
<div class="card">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span>
            <i class="ri-team-line me-2"></i>
            Participantes
        </span>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="ri-user-line me-2"></i>
                Total de participantes: 
                <span class="badge bg-primary">{{ $totalParticipantes }}</span>
            </h6>
            @if($curso->plazas > 0)
                <small class="text-muted">
                    {{ $curso->getInscritosCount() }} / {{ $curso->plazas }} plazas ocupadas
                </small>
            @endif
        </div>

        @if($totalParticipantes > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-success">
                        <tr>
                            <th width="40">#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th width="80">Rol</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($curso->personas as $index => $persona)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <i class="ri-user-line text-primary"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $persona->nombre ?? 'Sin nombre' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $persona->apellido1 ?? '' }} {{ $persona->apellido2 ?? '' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $persona->user->email ?? 'Sin email' }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $rolId = $persona->pivot->rol_participacion_id ?? null;
                                        $rolClass = $rolId == 1 ? 'bg-primary' : ($rolId == 2 ? 'bg-success' : 'bg-secondary');
                                        $rolText = $rolId == 1 ? 'Alumno' : ($rolId == 2 ? 'Profesor' : 'Otro');
                                    @endphp
                                    <span class="badge {{ $rolClass }}">{{ $rolText }}</span>
                                </td>
                                <td>
                                    @php
                                        $diplomaExiste = \App\Models\Diploma::existeParaParticipante($curso->id, $persona->id);
                                    @endphp
                                    
                                    @if($diplomaExiste)
                                        @php
                                            $diploma = \App\Models\Diploma::obtenerParaParticipante($curso->id, $persona->id);
                                        @endphp
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.cursos.diploma.participante.descargar', [$curso->id, $persona->id]) }}" 
                                               class="btn btn-success" 
                                               title="Descargar Diploma">
                                                <i class="ri-download-line"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-info ver-diploma-btn"
                                                    data-curso-id="{{ $curso->id }}"
                                                    data-persona-id="{{ $persona->id }}"
                                                    title="Ver Diploma">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                        </div>
                                    @else
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm generar-diploma-btn"
                                                data-curso-id="{{ $curso->id }}"
                                                data-persona-id="{{ $persona->id }}"
                                                data-persona-nombre="{{ $persona->nombre ?? 'Participante' }}"
                                                title="Generar Diploma">
                                            <i class="ri-file-text-line"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('admin.inscripciones.cursos.inscritos', $curso->id) }}" 
                   class="btn btn-outline-success btn-sm">
                    <i class="ri-eye-line me-1"></i>
                    Ver todos los inscritos
                </a>
                @if($curso->activo ?? $curso->estado ?? false)
                    <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="ri-user-add-line me-1"></i>
                        Añadir más
                    </a>
                @else
                    <button class="btn btn-outline-secondary btn-sm" disabled title="El curso está inactivo. No se pueden inscribir participantes.">
                        <i class="ri-user-add-line me-1"></i>
                        Añadir más
                    </button>
                @endif
                
                {{-- Botones de gestión masiva de diplomas --}}
                <button id="generar-todos-diplomas" class="btn btn-outline-warning btn-sm" 
                        data-curso-id="{{ $curso->id }}">
                    <i class="ri-file-text-line me-1"></i>
                    Generar todos los diplomas
                </button>
                
                <button id="descargar-todos-diplomas" class="btn btn-outline-info btn-sm"
                        data-curso-id="{{ $curso->id }}">
                    <i class="ri-download-line me-1"></i>
                    Descargar todos
                </button>
            </div>
        @else
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="ri-team-line text-muted fs-1"></i>
                </div>
                <div class="alert alert-info">
                    <i class="ri-information-line me-2"></i>
                    No hay participantes inscritos en este curso.
                </div>
                @if($curso->activo ?? $curso->estado ?? false)
                    <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
                       class="btn btn-success">
                        <i class="ri-user-add-line me-2"></i>
                        Inscribir primer participante
                    </a>
                @else
                    <button class="btn btn-secondary" disabled title="El curso está inactivo. No se pueden inscribir participantes.">
                        <i class="ri-user-add-line me-2"></i>
                        Inscribir primer participante
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 50%;
    font-size: 14px;
}

.generar-diploma-btn.loading {
    pointer-events: none;
    opacity: 0.6;
}

.generar-diploma-btn.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clics en botones de generar diploma individual
    document.querySelectorAll('.generar-diploma-btn').forEach(button => {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            const personaId = this.dataset.personaId;
            const personaNombre = this.dataset.personaNombre;
            
            // Confirmar acción
            if (!confirm(`¿Estás seguro de que quieres generar el diploma para ${personaNombre}?`)) {
                return;
            }
            
            // Mostrar estado de carga
            this.classList.add('loading');
            this.innerHTML = '<i class="ri-loader-4-line"></i>';
            this.disabled = true;
            
            // Realizar petición AJAX
            fetch(`/admin/cursos/${cursoId}/diploma/participante/${personaId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    alert('Diploma generado correctamente');
                    
                    // Recargar la página para mostrar el botón de descarga
                    window.location.reload();
                } else {
                    // Mostrar mensaje de error
                    alert('Error: ' + data.message);
                    
                    // Restaurar botón
                    this.classList.remove('loading');
                    this.innerHTML = '<i class="ri-file-text-line"></i>';
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el diploma. Inténtalo de nuevo.');
                
                // Restaurar botón
                this.classList.remove('loading');
                this.innerHTML = '<i class="ri-file-text-line"></i>';
                this.disabled = false;
            });
        });
    });

    // Manejar clics en botones de ver diploma
    document.querySelectorAll('.ver-diploma-btn').forEach(button => {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            const personaId = this.dataset.personaId;
            
            // Abrir el diploma en una nueva ventana
            window.open(`/admin/cursos/${cursoId}/diploma/participante/${personaId}/ver`, '_blank');
        });
    });

    // Manejar clic en "Generar todos los diplomas"
    const btnGenerarTodos = document.getElementById('generar-todos-diplomas');
    if (btnGenerarTodos) {
        btnGenerarTodos.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            
            if (!confirm('¿Estás seguro de que quieres generar diplomas para todos los participantes? Esto puede tomar varios minutos.')) {
                return;
            }
            
            // Mostrar estado de carga
            this.disabled = true;
            this.innerHTML = '<i class="ri-loader-4-line"></i> Generando...';
            
            // Realizar petición AJAX
            fetch(`/admin/cursos/${cursoId}/diplomas/generar-todos`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.count === 0) {
                        alert('Todos los diplomas de este curso ya están generados. No hay diplomas pendientes.');
                    } else {
                        alert(`Se generaron ${data.count} diplomas correctamente`);
                    }
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    this.disabled = false;
                    this.innerHTML = '<i class="ri-file-text-line me-1"></i> Generar todos los diplomas';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar los diplomas. Inténtalo de nuevo.');
                this.disabled = false;
                this.innerHTML = '<i class="ri-file-text-line me-1"></i> Generar todos los diplomas';
            });
        });
    }

    // Manejar clic en "Descargar todos"
    const btnDescargarTodos = document.getElementById('descargar-todos-diplomas');
    if (btnDescargarTodos) {
        btnDescargarTodos.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            
            if (!confirm('¿Estás seguro de que quieres descargar todos los diplomas de este curso?')) {
                return;
            }
            
            // Mostrar estado de carga
            this.disabled = true;
            this.innerHTML = '<i class="ri-loader-4-line"></i> Preparando descarga...';
            
            // Realizar petición AJAX para verificar si hay diplomas
            fetch(`/admin/cursos/${cursoId}/diplomas/verificar`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.count === 0) {
                    alert('No hay diplomas generados para este curso.');
                    this.disabled = false;
                    this.innerHTML = '<i class="ri-download-line me-1"></i> Descargar todos';
                } else {
                    // Descargar directamente el archivo ZIP
                    window.location.href = `/admin/cursos/${cursoId}/diplomas/descargar-todos`;
                    
                    // Restaurar botón después de un tiempo
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = '<i class="ri-download-line me-1"></i> Descargar todos';
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al verificar los diplomas. Inténtalo de nuevo.');
                this.disabled = false;
                this.innerHTML = '<i class="ri-download-line me-1"></i> Descargar todos';
            });
        });
    }
});
</script>