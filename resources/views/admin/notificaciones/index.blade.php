@extends('template.base')

@section('title', 'Notificaciones')
@section('title-sidebar', 'Notificaciones')
@section('title-page', 'Lista de Notificaciones')

@section('breadcrumb')
    <li class="breadcrumb-item active">Notificaciones</li>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Historial de Notificaciones</h1>
        <a href="{{ route('admin.notificaciones.create') }}" class="btn btn-primary">Nueva Notificación</a>
    </div>

    @include('template.partials.alerts')

    @if ($notifications->count())
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Usuarios</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notifications as $notification)
                    <tr>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->body }}</td>
                        <td>
                            @forelse ($notification->users() as $user)
                                <span class="badge bg-secondary">{{ $user->name }}</span>
                            @empty
                                <span class="text-muted">Sin destinatarios</span>
                            @endforelse
                        </td>
                        <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="alert alert-info">No se han enviado notificaciones aún.</div>
    @endif
</div>
@endsection
