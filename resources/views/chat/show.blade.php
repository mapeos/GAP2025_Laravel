@extends('template.base-alumno')
@section('title', 'Chat con ' . $user->name)
@section('title-page', 'Chat con ' . $user->name)
@section('content')
<div class="container my-4">
    <h4>Conversación con {{ $user->name }}</h4>
    <div class="card mb-3">
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            @forelse($mensajes as $mensaje)
                <div class="mb-2">
                    <strong>{{ $mensaje->senderId == auth()->id() ? 'Tú' : $user->name }}:</strong>
                    <span>{{ $mensaje->content }}</span>
                    <br>
                    <small class="text-muted">
                        @if($mensaje->createdAt)
                            {{ \Carbon\Carbon::parse($mensaje->createdAt)->format('d/m/Y H:i') }}
                        @endif
                    </small>
                </div>
            @empty
                <p class="text-muted">No hay mensajes aún.</p>
            @endforelse
        </div>
    </div>
    <form method="POST" action="{{ route('chat.store', $user->id) }}">
        @csrf
        <div class="input-group">
            <input type="text" name="mensaje" class="form-control" placeholder="Escribe un mensaje..." required maxlength="2000">
            <button class="btn btn-success" type="submit">Enviar</button>
        </div>
    </form>
    <a href="{{ route('chat.index') }}" class="btn btn-link mt-3">Volver a usuarios</a>
</div>
@endsection
