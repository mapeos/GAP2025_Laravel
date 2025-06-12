@extends('template.base')

@section('title', 'Crear Categoría')
@section('title-sidebar', 'Categorías')
@section('title-page', 'Crear Categoría')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.categorias.index') }}">Categorías</a></li>
<li class="breadcrumb-item active">Crear Categoría</li>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Nueva Categoría</h1>
    
    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    @include('template.partials.alerts')

    <form action="{{ route('admin.categorias.store') }}" method="POST" id="categoriaForm">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="45">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea id="descripcion" name="descripcion" class="form-control" maxlength="255">{{ old('descripcion') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary" id="cancelBtn">Cancelar</a>
    </form>
</div>

{{-- Modal de confirmación --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar salida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Hay cambios sin guardar. ¿Desea salir sin guardar los cambios?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, permanecer aquí</button>
                <a href="{{ route('admin.categorias.index') }}" class="btn btn-primary">Sí, salir sin guardar</a>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoriaForm = document.getElementById('categoriaForm');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        let hasUnsavedChanges = false;

        // Detectar cambios en el formulario
        const formInputs = categoriaForm.querySelectorAll('input, textarea');
        formInputs.forEach(input => {
            input.addEventListener('change', () => {
                hasUnsavedChanges = true;
            });
        });

        // Manejar el botón de cancelar
        cancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (hasUnsavedChanges) {
                confirmModal.show();
            } else {
                window.location.href = cancelBtn.href;
            }
        });
    });
</script>
@endpush
@endsection