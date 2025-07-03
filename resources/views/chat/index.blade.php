@extends('template.base-alumno')
@section('title', 'Chat')
@section('title-page', 'Chat entre usuarios')
@section('content')
<div class="d-inline-block mb-3">
  <a href="{{ route('alumno.home') }}" class="btn btn-outline-primary btn-sm">
    <i class="ri-arrow-go-back-line me-1"></i> Volver al inicio
  </a>
</div>
<div class="container my-4">
    <h5 class="mb-4">Mensajes recientes</h5>
    <ul class="list-group mb-4">
        @forelse($mensajesRecibidos as $mensaje)
            <li class="list-group-item">
                <strong>
                    @php
                        $otro = $mensaje->senderId == auth()->id() ? $mensaje->receiverId : $mensaje->senderId;
                        $usuario = collect($usuarios)->flatten(1)->firstWhere('id', $otro);
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
    <div class="row g-4 mb-4">
        @foreach(['profesor' => 'Profesores', 'alumno' => 'Alumnos'] as $rol => $titulo)
            @if(isset($usuarios[$rol]) && $usuarios[$rol]->count())
                <div class="col-12 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light fw-bold">{{ $titulo }}</div>
                        <div class="p-2">
                            <input type="text" class="form-control user-search-input" data-rol="{{ $rol }}" placeholder="Buscar por nombre...">
                        </div>
                        <div class="chat-pagination" id="chat-{{ $rol }}">
                            <div class="user-list-ajax" id="user-list-{{ $rol }}">
                                <ul class="list-group list-group-flush">
                                    @foreach($usuarios[$rol] as $usuario)
                                        @php $unread = $unreadCounts[$usuario->id] ?? 0; @endphp
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $usuario->name }}</span>
                                            <a href="{{ route('chat.show', $usuario->id) }}" class="btn btn-primary btn-sm">
                                                Chatear
                                                @if($unread > 0)
                                                    <span class="badge bg-danger ms-1">{{ $unread }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="card-footer bg-white border-0">
                                    {{ $usuarios[$rol]->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
@push('js')
<script src="{{ asset('js/chat-pagination.js') }}"></script>
@endpush
@push('scripts')
<script src="/js/chat-user-search.js"></script>
@endpush
