@extends('template.base')

@section('title', 'Crear Notificación por Email')
@section('title-sidebar', 'Email Notifications')
@section('title-page', 'Crear Notificación por Email')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.email-notifications.index') }}">Notificaciones por Email</a></li>
<li class="breadcrumb-item active">Crear Notificación</li>
@endsection

@push('css')
<style>
.preview-card {
    max-width: 400px;
    margin: 0 auto;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.preview-header {
    background: linear-gradient(135deg, #7533f9 0%, #3f78e0 100%);
    padding: 20px;
    text-align: center;
    color: white;
}

.preview-logo {
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
}

.preview-body {
    padding: 20px;
    background: white;
}

.preview-footer {
    background-color: #f8f9fa;
    padding: 15px;
    text-align: center;
    font-size: 12px;
    color: #6c757d;
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-mail-send-line me-2"></i>Crear Nueva Notificación por Email
                    </h5>
                </div>
                <div class="card-body">
                    @include('template.partials.alerts')

                    <form action="{{ route('admin.email-notifications.store') }}" method="POST" id="emailForm">
                        @csrf

                        <!-- Email Content -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Asunto del Email *</label>
                                    <input type="text" id="subject" name="subject" class="form-control" 
                                           value="{{ old('subject') }}" required maxlength="255"
                                           placeholder="Ej: Bienvenido a GAP 2025">
                                    <div class="form-text">El asunto aparecerá en la bandeja de entrada del usuario</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="greeting" class="form-label">Saludo</label>
                                    <input type="text" id="greeting" name="greeting" class="form-control" 
                                           value="{{ old('greeting', '¡Hola!') }}" maxlength="100"
                                           placeholder="¡Hola! / Estimado usuario">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="footer_text" class="form-label">Texto del Pie</label>
                                    <input type="text" id="footer_text" name="footer_text" class="form-control" 
                                           value="{{ old('footer_text', '¡Gracias por usar nuestra aplicación!') }}" 
                                           maxlength="255">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Mensaje del Email *</label>
                            <textarea id="body" name="body" class="form-control" rows="5" required 
                                      placeholder="Escribe aquí el contenido principal de tu email...">{{ old('body') }}</textarea>
                            <div class="form-text">Este será el contenido principal del email</div>
                        </div>

                        <!-- Action Button (Optional) -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="ri-external-link-line me-2"></i>Botón de Acción (Opcional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="action_text" class="form-label">Texto del Botón</label>
                                            <input type="text" id="action_text" name="action_text" class="form-control" 
                                                   value="{{ old('action_text') }}" maxlength="50"
                                                   placeholder="Ej: Ver Dashboard, Acceder al Curso">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="action_url" class="form-label">URL del Botón</label>
                                            <input type="url" id="action_url" name="action_url" class="form-control" 
                                                   value="{{ old('action_url') }}"
                                                   placeholder="https://ejemplo.com/dashboard">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <i class="ri-information-line me-1"></i>
                                    Si completas ambos campos, se mostrará un botón en el email
                                </div>
                            </div>
                        </div>

                        <!-- User Selection -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="ri-user-line me-2"></i>Seleccionar Destinatarios
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Quick Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="role_filter" class="form-label">Filtrar por Rol</label>
                                        <select id="role_filter" class="form-select">
                                            <option value="">Todos los roles</option>
                                            <option value="administrador">Administrador</option>
                                            <option value="profesor">Profesor</option>
                                            <option value="alumno">Alumno</option>
                                            <option value="facultativo">Facultativo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="status_filter" class="form-label">Estado</label>
                                        <select id="status_filter" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="1">Solo Activos</option>
                                            <option value="0">Solo Inactivos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-primary me-2" onclick="selectAll()">
                                                <i class="ri-check-double-line me-1"></i>Seleccionar Todos
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="clearAll()">
                                                <i class="ri-close-line me-1"></i>Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Users List -->
                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                    <div id="users-checkbox-list">
                                        @if(isset($users))
                                            @foreach ($users as $user)
                                                <div class="form-check user-checkbox mb-2"
                                                     data-role="{{ strtolower($user->getRoleNames()->first() ?? '') }}"
                                                     data-status="{{ $user->status ?? 0 }}">
                                                    <input class="form-check-input" type="checkbox" name="users[]" 
                                                           value="{{ $user->id }}" id="user_{{ $user->id }}">
                                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                                        <strong>{{ $user->name }}</strong> ({{ $user->email }})
                                                        @if($user->getRoleNames()->count())
                                                            <span class="badge bg-primary ms-2">{{ $user->getRoleNames()->first() }}</span>
                                                        @endif
                                                        @if($user->status == 1)
                                                            <span class="badge bg-success ms-1">Activo</span>
                                                        @else
                                                            <span class="badge bg-secondary ms-1">Inactivo</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-muted">No hay usuarios disponibles</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="ri-information-line me-1"></i>
                                    Selecciona los usuarios que recibirán la notificación por email
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.email-notifications.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-2"></i>Cancelar
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="sendTestEmail()">
                                    <i class="ri-test-tube-line me-2"></i>Enviar Prueba
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-mail-send-line me-2"></i>Enviar Notificación por Email
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Column -->
        <div class="col-lg-4">
            <div class="card sticky-top">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ri-eye-line me-2"></i>Vista Previa del Email
                    </h6>
                </div>
                <div class="card-body">
                    <div class="preview-card">
                        <div class="preview-header">
                            <div class="preview-logo">GAP</div>
                            <h6 class="mb-0">{{ config('app.name', 'GAP 2025') }}</h6>
                        </div>
                        <div class="preview-body">
                            <div class="fw-bold text-primary mb-2" id="preview-greeting">¡Hola!</div>
                            <div class="mb-3" id="preview-body">Este es el contenido de tu email...</div>
                            <div class="text-center" id="preview-action" style="display: none;">
                                <a href="#" class="btn btn-primary btn-sm" id="preview-button">Botón de Acción</a>
                            </div>
                        </div>
                        <div class="preview-footer" id="preview-footer">
                            ¡Gracias por usar nuestra aplicación!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// Real-time preview updates
document.addEventListener('DOMContentLoaded', function() {
    const subjectInput = document.getElementById('subject');
    const greetingInput = document.getElementById('greeting');
    const bodyInput = document.getElementById('body');
    const actionTextInput = document.getElementById('action_text');
    const actionUrlInput = document.getElementById('action_url');
    const footerInput = document.getElementById('footer_text');

    function updatePreview() {
        document.getElementById('preview-greeting').textContent = greetingInput.value || '¡Hola!';
        document.getElementById('preview-body').textContent = bodyInput.value || 'Este es el contenido de tu email...';
        document.getElementById('preview-footer').textContent = footerInput.value || '¡Gracias por usar nuestra aplicación!';
        
        const actionDiv = document.getElementById('preview-action');
        const actionButton = document.getElementById('preview-button');
        
        if (actionTextInput.value && actionUrlInput.value) {
            actionButton.textContent = actionTextInput.value;
            actionDiv.style.display = 'block';
        } else {
            actionDiv.style.display = 'none';
        }
    }

    [greetingInput, bodyInput, actionTextInput, actionUrlInput, footerInput].forEach(input => {
        input.addEventListener('input', updatePreview);
    });
});

// User filtering functions
function filterUsers() {
    const roleFilter = document.getElementById('role_filter').value.toLowerCase();
    const statusFilter = document.getElementById('status_filter').value;
    
    document.querySelectorAll('.user-checkbox').forEach(function(div) {
        let show = true;
        
        if (roleFilter && div.getAttribute('data-role') !== roleFilter) {
            show = false;
        }
        
        if (statusFilter && div.getAttribute('data-status') !== statusFilter) {
            show = false;
        }
        
        div.style.display = show ? '' : 'none';
    });
}

function selectAll() {
    document.querySelectorAll('.user-checkbox:not([style*="display: none"]) input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.checked = true;
    });
}

function clearAll() {
    document.querySelectorAll('.user-checkbox input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.checked = false;
    });
}

// Event listeners for filters
document.getElementById('role_filter').addEventListener('change', filterUsers);
document.getElementById('status_filter').addEventListener('change', filterUsers);

// Send test email function
function sendTestEmail() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('subject', document.getElementById('subject').value);
    formData.append('greeting', document.getElementById('greeting').value);
    formData.append('body', document.getElementById('body').value);
    formData.append('action_text', document.getElementById('action_text').value);
    formData.append('action_url', document.getElementById('action_url').value);
    formData.append('footer_text', document.getElementById('footer_text').value);

    // Show loading state
    const testBtn = event.target;
    const originalText = testBtn.innerHTML;
    testBtn.innerHTML = '<i class="ri-loader-4-line me-2 spinner-border spinner-border-sm"></i>Enviando...';
    testBtn.disabled = true;

    fetch('{{ route("admin.email-notifications.test") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="ri-check-line me-2"></i>${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('form'));

            // Auto-hide after 5 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        } else {
            // Show error alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="ri-error-warning-line me-2"></i>${data.message || 'Error al enviar email de prueba'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('form'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="ri-error-warning-line me-2"></i>Error de conexión al enviar email de prueba
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('form'));
    })
    .finally(() => {
        // Restore button state
        testBtn.innerHTML = originalText;
        testBtn.disabled = false;
    });
}
</script>
@endpush
@endsection
