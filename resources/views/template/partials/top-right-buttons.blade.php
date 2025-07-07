@if(Auth::check())
@php
    // Obtener los mensajes no leídos para el usuario autenticado
    $unreadService = app(\App\Application\Chat\GetUnreadCountForUser::class);
    $unreadCounts = $unreadService->execute(Auth::id());
    $totalUnread = array_sum($unreadCounts);
    $lastChatsService = app(\App\Application\Chat\GetLastChatsForUser::class);
    $mensajesRecientes = $lastChatsService->execute(Auth::id(), 5);
    $usuarios = \App\Models\User::whereIn('id', collect($mensajesRecientes)->map(fn($m) => $m->senderId == Auth::id() ? $m->receiverId : $m->senderId))->get();
@endphp
<!-- Theme Toggle Button -->
<button class="btn p-0 border-0 shadow-none" id="theme-toggle"><i class="ri-sun-line fs-5"></i></button>

<!-- Notifications Dropdown -->
<div class="notifications-dropdown">
    <button
        class="btn p-0 border-0 shadow-none position-relative"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="ri-notification-3-line fs-5"></i>
        @if($totalUnread > 0)
            <span class="badge topbar-badge bg-danger fw-medium position-absolute rounded-pill start-100 translate-middle">
                {{ $totalUnread }}
            </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end notifications-menu overflow-visible p-0">
        <!-- Header -->
        <div class="notifications-header p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0">Notificaciones</h6>
            <div class="d-flex align-items-center gap-2">
                @if($totalUnread > 0)
                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $totalUnread }} nuevo{{ $totalUnread > 1 ? 's' : '' }}</span>
                @endif
            </div>
        </div>
        <!-- Notifications List -->
        <div class="notifications-list" data-simplebar>
            @forelse($mensajesRecientes as $mensaje)
                @php
                    $otro = $mensaje->senderId == Auth::id() ? $mensaje->receiverId : $mensaje->senderId;
                    $usuario = $usuarios->firstWhere('id', $otro);
                    $unread = $unreadCounts[$otro] ?? 0;
                @endphp
                <a href="{{ route('chat.index', ['user_id' => $otro]) }}" class="dropdown-item notification-item px-3 py-2 border-bottom wa-chat-item" data-user-id="{{ $otro }}">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-info-subtle"><i class="ri-chat-3-line text-info"></i></div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fs-14">{{ $usuario ? $usuario->name : 'Usuario #' . $otro }}</h6>
                            <p class="text-muted mb-1 fs-12">{{ \Illuminate\Support\Str::limit($mensaje->content, 40) }}</p>
                            <small class="text-muted">
                                @if($mensaje->createdAt)
                                    {{ \Carbon\Carbon::parse($mensaje->createdAt)->diffForHumans() }}
                                @endif
                                @if($unread > 0)
                                    <span class="badge bg-danger ms-2">{{ $unread }}</span>
                                @endif
                            </small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="dropdown-item notification-item px-3 py-2 text-muted">No tienes mensajes nuevos.</div>
            @endforelse
        </div>
    </div>
</div>
<!-- User Profile Button -->
<div class="user-profile">
    <button
        class="btn p-0 border-0 shadow-none d-flex align-items-center"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#userProfileOffcanvas"
        aria-controls="userProfileOffcanvas">
        <div class="avatar position-relative">
            <img src="{{ Auth::user()->persona && Auth::user()->persona->foto_perfil ? asset('storage/' . Auth::user()->persona->foto_perfil) : asset('/admin/img/avatars/avatar2.jpg') }}" alt="User" class="rounded-circle" />
            <span class="online-indicator"></span>
        </div>
    </button>
</div>
@else
<!-- Theme Toggle Button for non-authenticated users -->
<button class="btn p-0 border-0 shadow-none" id="theme-toggle"><i class="ri-sun-line fs-5"></i></button>
@endif