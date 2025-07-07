@extends('template.base-alumno')
@section('title', 'Chat')
@section('title-page', 'Chat entre usuarios')
@section('breadcrumbs')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('alumno.home') }}">Inicio</a></li>
    <li class="breadcrumb-item active" aria-current="page">Chat entre usuarios</li>
  </ol>
</nav>
@endsection
@section('content')
{{-- Eliminar el botón de volver al inicio, ya que ahora se usa el breadcrumb --}}
<link rel="stylesheet" href="{{ asset('css/chat-wa.css') }}">
<div class="chat-wa-container">
    <div class="wa-sidebar">
        <div class="wa-sidebar-header">Chats recientes</div>
        <div class="wa-search">
            <input type="text" class="form-control user-search-input" placeholder="Buscar usuario o chat...">
        </div>
        <div class="wa-chats-list">
            @forelse($mensajesRecibidos as $mensaje)
                @php
                    $otro = $mensaje->senderId == auth()->id() ? $mensaje->receiverId : $mensaje->senderId;
                    $usuario = collect($usuarios)->flatten(1)->firstWhere('id', $otro);
                    $unread = $unreadCounts[$otro] ?? 0;
                    $foto = optional($usuario?->persona)->foto_perfil ?? null;
                @endphp
                <a href="javascript:void(0)" class="wa-chat-item" data-user-id="{{ $otro }}">
                    <span class="wa-chat-avatar">
                        @if($foto)
                            <img src="{{ asset('storage/' . $foto) }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                        @else
                            <i class="ri-user-3-line"></i>
                        @endif
                    </span>
                    <div class="wa-chat-info">
                        <div class="wa-chat-name">{{ $usuario ? $usuario->name : 'Usuario #' . $otro }}</div>
                        <div class="wa-chat-last">{{ \Illuminate\Support\Str::limit($mensaje->content, 32) }}</div>
                    </div>
                    <div class="text-end">
                        <div class="wa-chat-date">
                            @if($mensaje->createdAt)
                                {{ \Carbon\Carbon::parse($mensaje->createdAt)->diffForHumans() }}
                            @endif
                        </div>
                        @if($unread > 0)
                            <span class="wa-chat-unread">{{ $unread }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="text-muted p-3">No tienes mensajes recientes.</div>
            @endforelse
        </div>
        <div class="wa-sidebar-header mt-2">Usuarios disponibles</div>
        <div class="wa-chats-list">
            @foreach(['profesor' => 'Profesores', 'alumno' => 'Alumnos', 'otros' => 'Otros'] as $rol => $titulo)
                @if(isset($usuarios[$rol]) && $usuarios[$rol]->count())
                    <div class="mb-2 fw-bold text-secondary">{{ $titulo }}</div>
                    @foreach($usuarios[$rol] as $usuario)
                        @php 
                            $unread = $unreadCounts[$usuario->id] ?? 0; 
                            $foto = optional($usuario->persona)->foto_perfil;
                        @endphp
                        <a href="javascript:void(0)" class="wa-chat-item" data-user-id="{{ $usuario->id }}">
                            <span class="wa-chat-avatar">
                                @if($foto)
                                    <img src="{{ asset('storage/' . $foto) }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                @else
                                    <i class="ri-user-3-line"></i>
                                @endif
                            </span>
                            <div class="wa-chat-info">
                                <div class="wa-chat-name">{{ $usuario->name }}</div>
                            </div>
                            @if($unread > 0)
                                <span class="wa-chat-unread">{{ $unread }}</span>
                            @endif
                        </a>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
    <div class="wa-main">
        <h3>Selecciona un chat o usuario para comenzar a conversar</h3>
        <span class="text-muted">Inspirado en WhatsApp Web</span>
    </div>
</div>
@endsection
@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="{{ asset('js/chat-wa.js') }}"></script>
@endpush
