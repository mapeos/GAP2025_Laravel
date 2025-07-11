@extends('template.base-admin')

@section('title', 'Gestión de Diplomas')
@section('title-page', 'Gestión de Diplomas')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-3">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ri-home-2-line"></i> Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gestión de Diplomas</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="ri-award-line me-2"></i>
                        Gestión de Diplomas
                    </h4>
                    <div class="d-flex gap-2 align-items-center">
                        <button id="generar-todos-sistema" class="btn btn-warning btn-sm">
                            <i class="ri-file-text-line me-1"></i>
                            Generar todos los diplomas
                        </button>
                        <button id="descargar-todos-sistema" class="btn btn-info btn-sm">
                            <i class="ri-download-line me-1"></i>
                            Descargar todos
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tabla de diplomas -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tabla-diplomas">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Participante</th>
                                    <th>Email</th>
                                    <th>Curso</th>
                                    <th>Estado</th>
                                    <th>Fecha de Expedición</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participantes as $index => $participante)
                                    @php
                                        $diplomaExiste = \App\Models\Diploma::existeParaParticipante($participante->curso_id, $participante->persona_id);
                                        $diploma = $diplomaExiste ? \App\Models\Diploma::obtenerParaParticipante($participante->curso_id, $participante->persona_id) : null;
                                    @endphp
                                    <tr data-curso-id="{{ $participante->curso_id }}" data-persona-id="{{ $participante->persona_id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $participante->persona->nombre ?? '' }} {{ $participante->persona->apellido1 ?? '' }} {{ $participante->persona->apellido2 ?? '' }}</strong>
                                        </td>
                                        <td>{{ $participante->persona->user->email ?? 'Sin email' }}</td>
                                        <td>{{ $participante->curso->titulo }}</td>
                                        <td>
                                            @if($diplomaExiste)
                                                <span class="badge bg-success">Generado</span>
                                            @else
                                                <span class="badge bg-warning">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($diploma)
                                                {{ $diploma->fecha_expedicion->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($diplomaExiste)
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.cursos.diploma.participante.descargar', [$participante->curso_id, $participante->persona_id]) }}" 
                                                       class="btn btn-success" 
                                                       title="Descargar Diploma">
                                                        <i class="ri-download-line"></i>
                                                    </a>
                                                    <a href="{{ route('admin.cursos.diploma.participante.ver', [$participante->curso_id, $participante->persona_id]) }}" 
                                                       class="btn btn-info"
                                                       target="_blank"
                                                       title="Ver Diploma">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm generar-diploma-btn"
                                                        data-curso-id="{{ $participante->curso_id }}"
                                                        data-persona-id="{{ $participante->persona_id }}"
                                                        data-persona-nombre="{{ $participante->persona->nombre ?? 'Participante' }}"
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

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $participantes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirmar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que se encuentren los botones
    const btnGenerarTodos = document.getElementById('generar-todos-sistema');
    const btnDescargarTodos = document.getElementById('descargar-todos-sistema');
    
    // Verificar que los botones existan antes de agregar event listeners
    if (!btnGenerarTodos || !btnDescargarTodos) {
        console.error('No se encontraron los botones de gestión de diplomas');
        return;
    }
    
    // Manejar clics en botones de generar diploma individual
    document.querySelectorAll('.generar-diploma-btn').forEach(button => {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            const personaId = this.dataset.personaId;
            const personaNombre = this.dataset.personaNombre;
            
            if (!confirm(`¿Estás seguro de que quieres generar el diploma para ${personaNombre}?`)) {
                return;
            }
            
            this.classList.add('loading');
            this.innerHTML = '<i class="ri-loader-4-line"></i>';
            this.disabled = true;
            
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
                    alert('Diploma generado correctamente');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    this.classList.remove('loading');
                    this.innerHTML = '<i class="ri-file-text-line"></i>';
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el diploma. Inténtalo de nuevo.');
                this.classList.remove('loading');
                this.innerHTML = '<i class="ri-file-text-line"></i>';
                this.disabled = false;
            });
        });
    });

    // Generar todos los diplomas del sistema
    document.getElementById('generar-todos-sistema').addEventListener('click', function() {
        if (!confirm('¿Estás seguro de que quieres generar TODOS los diplomas pendientes? Esto puede tomar varios minutos.')) {
            return;
        }
        
        // Mostrar estado de carga
        this.disabled = true;
        this.innerHTML = '<i class="ri-loader-4-line"></i> Generando...';
        
        // Realizar petición AJAX
        fetch('/admin/diplomas/generar-todos', {
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
                    alert('Todos los diplomas del sistema ya están generados. No hay diplomas pendientes.');
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

    // Descargar todos los diplomas del sistema
    document.getElementById('descargar-todos-sistema').addEventListener('click', function() {
        if (!confirm('¿Estás seguro de que quieres descargar TODOS los diplomas generados?')) {
            return;
        }
        
        // Mostrar estado de carga
        this.disabled = true;
        this.innerHTML = '<i class="ri-loader-4-line"></i> Preparando descarga...';
        
        // Realizar petición AJAX para verificar si hay diplomas
        fetch('/admin/diplomas/verificar-diplomas', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.count === 0) {
                alert('No hay diplomas generados en el sistema para descargar.');
                this.disabled = false;
                this.innerHTML = '<i class="ri-download-line me-1"></i> Descargar todos';
            } else {
                // Descargar directamente el archivo ZIP
                window.location.href = '/admin/diplomas/descargar-todos';
                
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
});
</script>
@endpush 