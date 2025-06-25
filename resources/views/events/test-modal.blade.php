@extends('template.base')

@section('content')
<div class="container">
    <h1>Test Modal</h1>
    
    <button class="btn btn-primary" onclick="openTestModal()">
        Abrir Modal de Prueba
    </button>
    
    <!-- Modal de prueba -->
    <div class="modal fade" id="testModal" tabindex="-1" aria-labelledby="testModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testModalLabel">Modal de Prueba</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¡El modal funciona correctamente!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openTestModal() {
    console.log('openTestModal called');
    
    // Verificar si Bootstrap está disponible
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap not loaded');
        alert('Error: Bootstrap no está cargado');
        return;
    }
    
    // Verificar si el modal existe
    const modalElement = document.getElementById('testModal');
    if (!modalElement) {
        console.error('Modal element not found');
        alert('Error: Modal no encontrado');
        return;
    }
    
    console.log('Modal element found, opening...');
    
    // Abrir el modal
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}
</script>
@endsection 