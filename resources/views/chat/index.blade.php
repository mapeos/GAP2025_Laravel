@extends('template.base-alumno')
@section('title', 'Chat')
@section('title-page', 'Chat entre usuarios')
@section('content')
<div class="container my-4">
    <h5 class="mb-4">Mensajes recientes</h5>
    <ul class="list-group mb-4">
        @forelse($mensajesRecibidos as $mensaje)
            <li class="list-group-item">
                <strong>
                    @php
                        $otro = $mensaje->senderId == auth()->id() ? $mensaje->receiverId : $mensaje->senderId;
                        $usuario = $usuarios->firstWhere('id', $otro);
                        $unread = $unreadCounts[$otro] ?? 0;
                    @endphp
                    {{ $usuario ? $usuario->name : 'Usuario #' . $otro }}:
                </strong>
                {{ $mensaje->content }}<br>
                <small class="text-muted">
                    @if($mensaje->createdAt)
                        {{ \Carbon\Carbon::parse($mensaje->createdAt)->format('d/m/Y H:i') }}
                    @endif
                </small>
                <a href="{{ route('chat.show', $otro) }}" class="btn btn-link btn-sm">
                    Ver chat
                    @if($unread > 0)
                        <span class="badge bg-danger ms-1">{{ $unread }}</span>
                    @endif
                </a>
            </li>
        @empty
            <li class="list-group-item text-muted">No tienes mensajes recientes.</li>
        @endforelse
    </ul>
    <h4>Usuarios disponibles para chatear</h4>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        @foreach($usuarios as $usuario)
            @php
                $unread = $unreadCounts[$usuario->id] ?? 0;
            @endphp
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title mb-1">{{ $usuario->name }}</h5>
                            <p class="card-text text-muted small mb-2">{{ $usuario->email }}</p>
                        </div>
                        <div class="mt-auto d-flex align-items-center justify-content-between">
                            <a href="{{ route('chat.show', $usuario->id) }}" class="btn btn-primary btn-sm">
                                Chatear
                                @if($unread > 0)
                                    <span class="badge bg-danger ms-1">{{ $unread }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
