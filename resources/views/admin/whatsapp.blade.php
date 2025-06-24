@extends('template.base')

@section('content')
<div class="container mt-4">
    <h2>Enviar notificación WhatsApp (Plantilla)</h2>
    @if(session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.whatsapp.send') }}">
        @csrf
        <div class="mb-3">
            <label for="to" class="form-label">Número destino (con código país, sin +)</label>
            <input type="text" class="form-control" id="to" name="to" required placeholder="Ej: 34684245005">
        </div>
        <div class="mb-3">
            <label for="template_name" class="form-label">Nombre de la plantilla</label>
            <input type="text" class="form-control" id="template_name" name="template_name" value="hello_world" required>
        </div>
        <div class="mb-3">
            <label for="language_code" class="form-label">Código de idioma</label>
            <input type="text" class="form-control" id="language_code" name="language_code" value="en_US" required>
        </div>
        <button type="submit" class="btn btn-success">Enviar WhatsApp</button>
    </form>
</div>
@endsection
