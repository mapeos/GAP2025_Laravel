<div class="user-profile p-1">
    <div class="d-flex align-items-center" data-bs-toggle="dropdown">
        <div class="avatar position-relative">
            <img src="{{ asset('/admin/img/gap_ico.png') }}" alt="User" class="rounded-circle" width="36" height="36" />
            <span class="online-indicator"></span>
        </div>
        <div class="user-info ms-3">
            <span class="fw-semibold">{{ Auth::user()->name ?? ''}}</span>
            <small class="text-muted d-block" style="margin-top: -0.25rem">{{ Auth::user()->email ?? '' }}</small>
        </div>
        <i class="ri-expand-up-down-line ms-auto fs-6"></i>
    </div>
    <ul class="dropdown-menu py-2">
        <li class="border-0 border-bottom border-dashed pb-2">
            <div class="d-flex align-items-center">
                <div class="avatar position-relative">
                    <img
                        src="{{ asset('/admin/img/gap_ico.png') }}"
                        alt="User"
                        class="rounded-circle"
                        width="40"
                        height="40" />
                    <span class="online-indicator"></span>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0 fw-semibold">{{ Auth::user()->name ?? ''}}</h6>
                    <small class="text-muted">{{ Auth::user()->email ?? '' }}</small>
                </div>
            </div>
        </li>
        <li class="border-0 border-bottom border-dashed py-1">
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('alumno.home') }}">
                <i class="ri-home-line"></i> <span>Inicio</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.show') }}">
                <i class="ri-user-line"></i> <span>Mi Perfil</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                <i class="ri-edit-line"></i> <span>Editar Perfil</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('chat.index') }}">
                <i class="ri-chat-3-line"></i> <span>Chat</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('events.calendar') }}">
                <i class="ri-calendar-line"></i> <span>Eventos</span>
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="ri-logout-box-line"></i> <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form> 