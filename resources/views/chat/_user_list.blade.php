@php $unreadCounts = $unreadCounts ?? []; @endphp
<ul class="list-group list-group-flush">
    @forelse($usuarios as $usuario)
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
    @empty
        <li class="list-group-item text-muted">No hay usuarios disponibles.</li>
    @endforelse
</ul>
<div class="card-footer bg-white border-0">
    {!! $usuarios->appends(request()->except('page'))->withQueryString()->links('vendor.pagination.ajax') !!}
</div>
