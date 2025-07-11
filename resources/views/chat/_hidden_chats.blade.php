@php
    use App\Models\User;
    use App\Models\ChatHidden;
    $chatsOcultos = ChatHidden::where('user_id', auth()->id())->get();
    $usuariosOcultos = User::whereIn('id', $chatsOcultos->pluck('other_user_id'))->get();
@endphp
@if($usuariosOcultos->count())
<div class="card mb-4">
    <div class="card-header bg-light fw-bold">Chats ocultos</div>
    <ul class="list-group list-group-flush">
        @foreach($usuariosOcultos as $usuario)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ $usuario->name }}</span>
                <form method="POST" action="{{ route('chat.unhide', $usuario->id) }}" class="d-inline unhide-chat-form" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="ri-eye-line"></i> Mostrar
                    </button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endif
<script>
$(function() {
    $('.unhide-chat-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        $.post($form.attr('action'), $form.serialize(), function(resp) {
            if(resp.success) {
                $form.closest('li').fadeOut();
            }
        });
    });
});
</script>
