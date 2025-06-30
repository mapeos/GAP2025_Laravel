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
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="alert alert-welcome p-4 rounded-3">
            <h4 class="alert-heading mb-3">Bienvenido, {{ Auth::user()->name }}</h4>
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
                @php
                    $persona = Auth::user()->persona;
                    $misCursos = $persona ? $persona->cursos()->get() : collect();
                @endphp
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
                @php
                    // Obtener los últimos chats usando el mismo método que en el index
                    $chatService = app(\App\Application\Chat\GetLastChatsForUser::class);
                    $mensajesRecientes = $chatService->execute(Auth::id(), 5);
                    $usuarios = \App\Models\User::whereIn('id', collect($mensajesRecientes)->map(fn($m) => $m->senderId == Auth::id() ? $m->receiverId : $m->senderId))->get();
                @endphp
                <ul class="list-group list-group-flush">
                    @forelse($mensajesRecientes as $mensaje)
                        @php
                            $otro = $mensaje->senderId == Auth::id() ? $mensaje->receiverId : $mensaje->senderId;
                            $usuario = $usuarios->firstWhere('id', $otro);
                        @endphp
                        <li class="list-group-item px-0 py-1">
                            <a href="{{ route('chat.show', $otro) }}" class="text-decoration-none">
                                <strong>{{ $usuario ? $usuario->name : 'Usuario #' . $otro }}</strong>:
                                {{ \Illuminate\Support\Str::limit($mensaje->content, 30) }}
                                <br>
                                <small class="text-muted">
                                    @if($mensaje->createdAt)
                                        {{ \Carbon\Carbon::parse($mensaje->createdAt)->format('d/m/Y H:i') }}
                                    @endif
                                </small>
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
                    <strong>Nombre:</strong> {{ Auth::user()->name }}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> {{ Auth::user()->email }}
                </div>
                @if(Auth::user()->persona)
                    <div class="mb-3">
                        <strong>DNI:</strong> {{ Auth::user()->persona->dni }}
                    </div>
                    <div class="mb-3">
                        <strong>Teléfono:</strong> {{ Auth::user()->persona->tfno ?? 'No especificado' }}
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
                <h5 class="card-title mb-0">Mis próximas citas</h5>
            </div>
            <div class="card-body text-start">
                @php
                    $proximasCitas = \App\Models\EventoParticipante::with('evento')
                        ->where('user_id', Auth::id())
                        ->whereHas('evento', function($q) { $q->where('fecha_inicio', '>=', now()); })
                        ->orderByDesc('id')
                        ->take(3)
                        ->get();
                @endphp
                <i class="ri-calendar-event-line display-4 mb-3 text-warning"></i>
                @if($proximasCitas->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($proximasCitas as $cita)
                            <li class="mb-3">
                                <strong>{{ $cita->evento->titulo ?? 'Evento eliminado' }}</strong><br>
                                <small class="text-muted">
                                    {{ $cita->evento && $cita->evento->fecha_inicio ? $cita->evento->fecha_inicio->format('d/m/Y H:i') : '' }}
                            </small><br>
                            <span class="badge bg-secondary">{{ $cita->rol }}</span>
                            @if($cita->evento && $cita->evento->ubicacion)
                                <span class="badge bg-light text-dark">{{ $cita->evento->ubicacion }}</span>
                            @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mb-0">No tienes citas agendadas próximamente.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
