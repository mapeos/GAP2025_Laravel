@extends('template.base-alumno')

@section('title', 'Alumno')
@section('title-page', 'Home')

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-3">
        <li class="breadcrumb-item active" aria-current="page">
            <i class="ri-home-2-line"></i> Inicio
        </li>
    </ol>
</nav>
@endsection

@section('content')
@php
    $user = Auth::user();
    $persona = $user ? $user->persona : null;
    $misCursos = $persona ? $persona->cursos()->get() : collect();
    $eventosFuturos = collect();
    $profesores = collect();
    // Obtener chats recientes y usuarios igual que en el notifications dropdown
    $unreadService = app(\App\Application\Chat\GetUnreadCountForUser::class);
    $unreadCounts = $unreadService->execute(Auth::id());
    $lastChatsService = app(\App\Application\Chat\GetLastChatsForUser::class);
    $mensajesRecientes = $lastChatsService->execute(Auth::id(), 5);
    $usuariosChat = \App\Models\User::whereIn('id', collect($mensajesRecientes)->map(fn($m) => $m->senderId == Auth::id() ? $m->receiverId : $m->senderId))->get();
    // Obtener próximos eventos donde el usuario es participante o creador
    // ID del tipo de evento común (Clase, Entrega, Reunión)
    $tiposComunes = [15, 16, 17]; // Clase, Entrega, Reunión
    $proximosEventos = \App\Models\Evento::where(function($q) use ($user, $tiposComunes) {
        $q->whereHas('participantes', function($q2) use ($user) {
            $q2->where('user_id', $user->id);
        })
        ->orWhere('creado_por', $user->id)
        ->orWhereIn('tipo_evento_id', $tiposComunes);
    })
    ->where('fecha_inicio', '>=', now())
    ->orderBy('fecha_inicio')
    ->limit(5)
    ->get();
    // Obtener próximas citas donde el usuario es alumno (solo sus citas, no eventos de tipo cita)
    $proximasCitas = \App\Models\SolicitudCita::where('alumno_id', $user->id)
        ->where('fecha_propuesta', '>=', now())
        ->orderBy('fecha_propuesta')
        ->limit(5)
        ->get();
@endphp

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="alert alert-welcome p-4 rounded-3">
            <h4 class="alert-heading mb-3">Bienvenido, {{ $user ? $user->name : 'Usuario' }}</h4>
            <p class="mb-0">Este es tu panel de control personal. Aquí podrás gestionar tu perfil y acceder a todas tus funciones.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-hover h-100 border-primary">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="ri-book-open-line me-2"></i>
                <h5 class="card-title mb-0">Mis cursos</h5>
            </div>
            <div class="card-body text-start text-muted">
                <i class="ri-graduation-cap-line display-4 mb-3"></i>
                @if($misCursos->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($misCursos as $curso)
                            <li class="mb-2">
                                <i class="ri-book-open-line text-primary me-1"></i>
                                <strong>{{ $curso->titulo }}</strong>
                                <br>
                                <small class="text-muted">{{ $curso->descripcion }}</small>
                                <br>
                                @php
                                    $estado = $curso->pivot->estado ?? 'desconocido';
                                @endphp
                                <span class="badge 
                                    @if($estado == 'activo') bg-success
                                    @elseif($estado == 'pendiente') bg-warning text-dark
                                    @elseif($estado == 'espera') bg-info text-dark
                                    @elseif($estado == 'rechazado') bg-danger
                                    @else bg-secondary
                                    @endif
                                ">
                                    @if($estado == 'activo') Aceptado
                                    @elseif($estado == 'pendiente') Pendiente
                                    @elseif($estado == 'espera') En espera
                                    @elseif($estado == 'rechazado') Rechazado
                                    @else Desconocido
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mb-0">No estás inscrito en ningún curso.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover h-100 border-success">
            <div class="card-header bg-success text-white d-flex align-items-center">
                <i class="ri-add-circle-line me-2"></i>
                <h5 class="card-title mb-0">Cursos disponibles</h5>
            </div>
            <div class="card-body text-start">
                @php
                    $cursosDisponibles = \App\Models\Curso::where('estado', 'activo')
                        ->whereNotIn('id', $misCursos->pluck('id'))
                        ->select('id', 'titulo', 'descripcion')
                        ->get();
                @endphp
                <i class="ri-booklet-line display-4 mb-3 text-success"></i>
                @if($cursosDisponibles->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($cursosDisponibles as $curso)
                            <li class="mb-3">
                                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                                    <div>
                                        <i class="ri-add-circle-line text-success me-1"></i>
                                        <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="fw-bold text-decoration-underline">{{ $curso->titulo }}</a>
                                        <br>
                                        <small class="text-muted">{{ $curso->descripcion }}</small>
                                    </div>
                                    {{-- Botón Inscribirme eliminado, ya que aparece en la vista del curso --}}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mb-0">No hay cursos disponibles para inscribirse.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover h-100 border-info">
            <div class="card-header bg-info text-white d-flex align-items-center">
                <i class="ri-file-list-3-line me-2"></i>
                <h5 class="card-title mb-0">Temarios de mis cursos</h5>
            </div>
            <div class="card-body text-start text-muted">
                <i class="ri-file-download-line display-4 mb-3"></i>
                @if($misCursos->count())
                    <ul class="list-unstyled mb-0">
                        @php $hayTemario = false; @endphp
                        @foreach($misCursos as $curso)
                            @if(!empty($curso->temario_path))
                                @php $hayTemario = true; @endphp
                                <li class="mb-2">
                                    <i class="ri-file-list-3-line text-info me-1"></i>
                                    <strong>{{ $curso->titulo }}</strong>
                                    <a href="{{ asset('storage/' . $curso->temario_path) }}" class="btn btn-outline-info btn-sm ms-2" download>
                                        <i class="ri-download-2-line"></i> Descargar temario
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        @if(!$hayTemario)
                            <li class="text-muted">Ningún curso tiene temario disponible.</li>
                        @endif
                    </ul>
                @else
                    <p class="mb-0">No estás inscrito en ningún curso.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-md-4">
        <div class="card card-hover h-100 border-info">
            <div class="card-header bg-info text-white d-flex align-items-center">
                <i class="ri-chat-3-line me-2"></i>
                <h5 class="card-title mb-0">Chat</h5>
            </div>
            <div class="card-body text-center">
                <h6 class="mb-2">Chats recientes</h6>
                <ul class="list-group list-group-flush">
                    @forelse($mensajesRecientes as $mensaje)
                        @php
                            $otro = $mensaje->senderId == Auth::id() ? $mensaje->receiverId : $mensaje->senderId;
                            $usuario = $usuariosChat->firstWhere('id', $otro);
                            $unread = $unreadCounts[$otro] ?? 0;
                        @endphp
                        <li class="list-group-item px-0 py-1">
                            <a href="{{ route('chat.index', ['user_id' => $otro]) }}" class="d-flex align-items-center text-decoration-none wa-chat-item" data-user-id="{{ $otro }}">
                                <div class="avatar avatar-sm bg-info-subtle me-2"><i class="ri-chat-3-line text-info"></i></div>
                                <div class="flex-grow-1 text-start">
                                    <strong>{{ $usuario ? $usuario->name : 'Usuario #' . $otro }}</strong>
                                    @if($unread > 0)
                                        <span class="badge bg-danger ms-2">{{ $unread }}</span>
                                    @endif
                                    <div class="small text-muted">
                                        {{ \Illuminate\Support\Str::limit($mensaje->content, 40) }}<br>
                                        @if($mensaje->createdAt)
                                            <span>{{ \Carbon\Carbon::parse($mensaje->createdAt)->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item text-muted px-0 py-1">No tienes chats recientes.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover h-100">
            <div class="card-header border-0 bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="ri-user-line me-2"></i>
                    <h5 class="card-title mb-0">Mi Perfil</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Nombre:</strong> {{ $user->name }}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> {{ $user->email }}
                </div>
                @if($persona)
                    <div class="mb-3">
                        <strong>DNI:</strong> {{ $persona->dni }}
                    </div>
                    <div class="mb-3">
                        <strong>Teléfono:</strong> {{ $persona->tfno ?? 'No especificado' }}
                    </div>
                @else
                    <div class="alert alert-info">
                        Completa tu información personal para acceder a todas las funcionalidades.
                    </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('profile.show') }}" class="btn btn-primary btn-sm">
                        <i class="ri-user-line me-1"></i> Ver Perfil Completo
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn btn-secondary btn-sm">
                        <i class="ri-user-settings-line me-1"></i> Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-hover h-100 border-warning">
            <div class="card-header bg-warning text-dark d-flex align-items-center">
                <i class="ri-calendar-check-line me-2"></i>
                <h5 class="card-title mb-0">Mi Agenda</h5>
            </div>
            <div class="card-body text-start">
                <ul class="list-group list-group-flush mb-3">
                    @forelse($proximosEventos as $evento)
                        <li class="list-group-item d-flex align-items-center">
                            <i class="ri-calendar-event-line text-primary me-2"></i>
                            <div>
                                <strong>{{ $evento->titulo }}</strong><br>
                                <small class="text-muted">Evento - {{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y H:i') }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No tienes eventos próximos.</li>
                    @endforelse
                    @forelse($proximasCitas as $cita)
                        <li class="list-group-item d-flex align-items-center">
                            <i class="ri-stethoscope-line text-success me-2"></i>
                            <div>
                                <strong>Cita: {{ $cita->motivo ?? 'Consulta' }}</strong><br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($cita->fecha_propuesta)->format('d/m/Y H:i') }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No tienes citas próximas.</li>
                    @endforelse
                </ul>
                <div class="text-center py-2">
                    <a href="{{ route('events.calendar') }}" class="btn btn-warning btn-sm">
                        <i class="ri-calendar-line me-1"></i> Ver Calendario
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
