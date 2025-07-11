@extends('template.base')

@inject('storage', 'Illuminate\Support\Facades\Storage')

@section('title', 'Detalles del Curso')

@section('content')
<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="ri-eye-line me-2 text-primary"></i>
                Detalles del Curso
            </h1>
            <p class="text-muted mb-0">Información completa y estado del curso</p>
</div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cursos.edit', $curso->id) }}" class="btn btn-primary">
                <i class="ri-edit-line me-2"></i>
                Editar Curso
            </a>
            <a href="{{ route('admin.cursos.diploma', $curso->id) }}" class="btn btn-success">
                <i class="ri-award-line me-2"></i>
                Generar Diploma
            </a>
            <a href="{{ route('admin.cursos.index') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-2"></i>
                Volver a Cursos
            </a>
</div>
    </div>

    <!-- Mensajes flash -->
    @include('template.partials.alerts')

    <!-- Alertas de estado -->
    @include('admin.cursos.parts.status-alerts', ['curso' => $curso])

    <!-- Estadísticas del curso -->
    @include('admin.cursos.parts.course-stats', ['curso' => $curso])

    <div class="row">
        <!-- Información principal del curso -->
        <div class="col-lg-8">
            <!-- Detalles completos del curso -->
            @include('admin.cursos.parts.course-details', ['curso' => $curso])
            
            <!-- Participantes -->
            @include('admin.cursos.parts.participante', ['curso' => $curso])

            <!-- Usuarios que han pagado este curso y resumen financiero (solo admin) -->
            @if(auth()->user() && auth()->user()->hasRole('Administrador'))
                <div class="card shadow-sm border-0 mb-4" style="margin-top:-2rem;">
                    <div class="card-header bg-white border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <h5 class="mb-0 text-primary">
                            <i class="ri-money-euro-circle-line me-2"></i>
                            Usuarios que han pagado este curso
                        </h5>
                        <div class="mt-3 mt-md-0">
                            <span class="badge bg-success fs-6" style="font-size:1.1em;padding:0.7em 1.3em;">
                                <i class="ri-coins-line me-1"></i>
                                Total pagado: €{{ number_format($curso->pagos->sum('importe'), 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Buscador mejorado -->
                        <form method="GET" class="mb-4">
                            <div class="input-group" style="max-width: 500px; margin-bottom: 1rem;">
                                <input type="text" name="buscar_usuario" value="{{ request('buscar_usuario') }}" class="form-control" placeholder="Buscar por nombre, DNI o email..." style="border-radius: 8px 0 0 8px;">
                                <input type="date" name="buscar_fecha" value="{{ request('buscar_fecha') }}" class="form-control" style="max-width: 180px; border-radius: 0;">
                                <button type="submit" class="btn btn-primary" style="border-radius: 0 8px 8px 0;">Buscar</button>
                            </div>
                        </form>
                        @php
                            $pagosFiltrados = $curso->pagos->filter(function($pago) {
                                $usuario = request('buscar_usuario');
                                $fecha = request('buscar_fecha');
                                $match = true;
                                if ($usuario) {
                                    $nombre = $pago->persona->nombre ?? $pago->nombre ?? '';
                                    $dni = $pago->persona->dni ?? '';
                                    $email = $pago->email ?? '';
                                    $match = $match && (
                                        stripos($nombre, $usuario) !== false ||
                                        stripos($dni, $usuario) !== false ||
                                        stripos($email, $usuario) !== false
                                    );
                                }
                                if ($fecha) {
                                    $match = $match && ($pago->fecha && \Carbon\Carbon::parse($pago->fecha)->format('Y-m-d') == $fecha);
                                }
                                return $match;
                            });
                        @endphp
                        @if($pagosFiltrados->count())
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>DNI</th>
                                            <th>Email</th>
                                            <th>Rol</th>
                                            <th>Fecha de pago</th>
                                            <th>Factura</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pagosFiltrados as $pago)
                                            <tr>
                                                <td>{{ $pago->persona->nombre ?? $pago->nombre ?? '-' }}</td>
                                                <td>{{ $pago->persona->dni ?? '-' }}</td>
                                                <td>{{ $pago->email ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $user = $pago->factura ? $pago->factura->user : null;
                                                    @endphp
                                                    @if($user && method_exists($user, 'hasRole'))
                                                        @if($user->hasRole('Administrador'))
                                                            Administrador
                                                        @elseif($user->hasRole('Profesor'))
                                                            Profesor
                                                        @elseif($user->hasRole('Alumno'))
                                                            Alumno
                                                        @elseif($user->hasRole('Editor'))
                                                            Editor
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $pago->fecha ? \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    @if($pago->factura)
                                                        <a href="{{ route('admin.pagos.facturas.index', ['factura_id' => $pago->factura->id]) }}" class="btn btn-sm btn-info">Ver factura</a>
                                                    @else
                                                        <span class="text-muted">No disponible</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">No hay pagos registrados para este curso con los filtros aplicados.</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Panel lateral - Recursos del curso -->
        <div class="col-lg-4">
            <!-- Temario -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ri-file-text-line me-2"></i>
                        Temario del Curso
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if ($curso->temario_path && Storage::disk('public')->exists($curso->temario_path))
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="ri-file-pdf-line text-info fs-1"></i>
                            </div>
                            <h6 class="text-success mb-3">
                                <i class="ri-check-circle-line me-2"></i>
                                Temario Disponible
                            </h6>
                            <a href="{{ asset('storage/' . $curso->temario_path) }}" 
                               target="_blank" 
                               class="btn btn-info w-100 mb-3">
                                <i class="ri-download-line me-2"></i> 
                                Ver/Descargar Temario
                            </a>
                            <div class="text-muted small">
                                <i class="ri-time-line me-1"></i>
                                Subido el {{ \Carbon\Carbon::parse(Storage::disk('public')->lastModified($curso->temario_path))->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="ri-file-text-line text-muted fs-1"></i>
                            </div>
                            <div class="alert alert-warning border-0">
                                <i class="ri-alert-line me-2"></i>
                                <strong>Sin temario</strong><br>
                                <small>Este curso aún no tiene un temario subido.</small>
                            </div>
                            <a href="{{ route('admin.cursos.edit', $curso->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ri-edit-line me-2"></i>
                                Agregar Temario
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Portada -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="ri-image-line me-2"></i>
                        Imagen de Portada
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if ($curso->portada_path && Storage::disk('public')->exists($curso->portada_path))
                        <div class="text-center">
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $curso->portada_path) }}" 
                                     alt="Portada del curso" 
                                     class="img-fluid rounded shadow-sm" 
                                     style="max-height: 200px; max-width: 100%;">
                            </div>
                            <h6 class="text-success mb-3">
                                <i class="ri-check-circle-line me-2"></i>
                                Portada Disponible
                            </h6>
                            <a href="{{ asset('storage/' . $curso->portada_path) }}" 
                               target="_blank" 
                               class="btn btn-primary w-100 mb-3">
                                <i class="ri-external-link-line me-2"></i> 
                                Ver Portada Completa
                            </a>
                            <div class="text-muted small">
                                <i class="ri-time-line me-1"></i>
                                Subida el {{ \Carbon\Carbon::parse(Storage::disk('public')->lastModified($curso->portada_path))->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="ri-image-line text-muted fs-1"></i>
                            </div>
                            <div class="alert alert-warning border-0">
                                <i class="ri-alert-line me-2"></i>
                                <strong>Sin portada</strong><br>
                                <small>Este curso aún no tiene una imagen de portada.</small>
                            </div>
                            <a href="{{ route('admin.cursos.edit', $curso->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ri-edit-line me-2"></i>
                                Agregar Portada
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


@endsection