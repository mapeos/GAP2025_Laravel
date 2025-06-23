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

{{-- Gestión de participantes del curso --}}
<div class="card h-100">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <span>
            <i class="ri-team-line me-2"></i>
            Participantes
        </span>
        <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
           class="btn btn-warning btn-sm fw-bold shadow"
           title="Añadir participante">
            <i class="ri-user-add-line me-1"></i> 
            Añadir
        </a>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                <i class="ri-user-line me-2"></i>
                Total de participantes: 
                <span class="badge bg-primary">{{ $curso->personas->count() ?? 0 }}</span>
            </h6>
        </div>

        @if($curso->personas && $curso->personas->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-success">
                        <tr>
                            <th width="40">#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th width="80">Rol</th>
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
                                            <strong>{{ $persona->nombre }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $persona->apellido1 }} {{ $persona->apellido2 }}
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('admin.inscripciones.cursos.inscritos', $curso->id) }}" 
                   class="btn btn-outline-success btn-sm">
                    <i class="ri-eye-line me-1"></i>
                    Ver todos los inscritos
                </a>
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
                <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
                   class="btn btn-success">
                    <i class="ri-user-add-line me-2"></i>
                    Inscribir primer participante
                </a>
            </div>
        @endif
    </div>
</div>