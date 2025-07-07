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
<link rel="stylesheet" href="{{ asset('css/dashboard-alumno.css') }}">
<div class="dashboard-alumno-bg">
    <div class="dashboard-grid-alumno">
        <!-- Mis cursos (2x2) -->
        <div class="dashboard-card dashboard-card-main">
            <div class="card card-modern h-100">
                <div class="card-header">
                    <span class="icon-bg"><i class="ri-book-open-line"></i></span>
                    <span class="card-title">Mis cursos</span>
                </div>
                <div class="card-body text-start text-muted position-relative">
                    {{-- <span class="display-4"><i class="ri-graduation-cap-line"></i></span> --}} 
                    @if($misCursos->count())
                        <ul>
                            @foreach($misCursos as $curso)
                                <li>
                                    <i class="ri-book-open-line text-primary me-1"></i>
                                    <strong>{{ $curso->titulo }}</strong><br>
                                    <small class="text-muted">{{ $curso->descripcion }}</small><br>
                                    @php $estado = $curso->pivot->estado ?? 'desconocido'; @endphp
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
        <!-- Chat (2x1 a la derecha) -->
        <div class="dashboard-card dashboard-card-chat">
            <div class="card card-modern h-100">
                <div class="card-header">
                    <span class="icon-bg"><i class="ri-chat-3-line"></i></span>
                    <span class="card-title">Chat</span>
                </div>
                <div class="card-body text-center position-relative">
                    {{-- <span class="display-4 text-info"><i class="ri-chat-3-line"></i></span> --}} 
                    <h6 class="mb-3 mt-2">Chats recientes</h6>
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
        <!-- Mi Perfil -->
        <div class="dashboard-card dashboard-card-perfil">
            <div class="card card-modern h-100">
                <div class="card-header">
                    <span class="icon-bg"><i class="ri-user-line"></i></span>
                    <span class="card-title">Mi Perfil</span>
                </div>
                <div class="card-body position-relative">
                    {{-- <span class="display-4 text-primary"><i class="ri-user-line"></i></span> --}} 
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
        <!-- Mi Agenda -->
        <div class="dashboard-card dashboard-card-agenda">
            <div class="card card-modern h-100">
                <div class="card-header">
                    <span class="icon-bg"><i class="ri-calendar-check-line"></i></span>
                    <span class="card-title">Mi Agenda</span>
                </div>
                <div class="card-body position-relative">
                    {{-- <span class="display-4 text-warning"><i class="ri-calendar-check-line"></i></span> --}} 
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
        <!-- Cursos disponibles -->
        <div class="dashboard-card dashboard-card-cursos">
            <div class="card card-modern h-100">
                <div class="card-header">
                    <span class="icon-bg"><i class="ri-add-circle-line"></i></span>
                    <span class="card-title">Cursos disponibles</span>
                </div>
                <div class="card-body text-start position-relative">
                    {{-- <span class="display-4 text-success"><i class="ri-booklet-line"></i></span> --}} 
                    @if($cursosDisponibles->count())
                        <ul>
                            @foreach($cursosDisponibles as $curso)
                                <li>
                                    <i class="ri-add-circle-line text-success me-1"></i>
                                    <a href="{{ route('alumno.cursos.show', $curso->id) }}" class="fw-bold text-decoration-underline">{{ $curso->titulo }}</a><br>
                                    <small class="text-muted">{{ $curso->descripcion }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0">No hay cursos disponibles para inscribirse.</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Temarios de mis cursos -->
        <div class="dashboard-card dashboard-card-temarios">
            <div class="card card-modern h-100">
                <div class="card-header">
                    <span class="icon-bg"><i class="ri-file-list-3-line"></i></span>
                    <span class="card-title">Temarios de mis cursos</span>
                </div>
                <div class="card-body text-start text-muted position-relative">
                    {{-- <span class="display-4 text-info"><i class="ri-file-download-line"></i></span> --}} 
                    @if($misCursos->count())
                        <ul>
                            @php $hayTemario = false; @endphp
                            @foreach($misCursos as $curso)
                                @if(!empty($curso->temario_path))
                                    @php $hayTemario = true; @endphp
                                    <li>
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
</div>
@endsection
