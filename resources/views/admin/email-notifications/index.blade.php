@extends('template.base')

@section('title', 'Notificaciones por Email')
@section('title-sidebar', 'Email Notifications')
@section('title-page', 'Gestión de Notificaciones por Email')

@section('breadcrumb')
    <li class="breadcrumb-item active">Notificaciones por Email</li>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Historial de Notificaciones por Email</h1>
        <a href="{{ route('admin.email-notifications.create') }}" class="btn btn-primary">
            <i class="ri-mail-send-line me-2"></i>Nueva Notificación por Email
        </a>
    </div>

    @include('template.partials.alerts')

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Enviados</h6>
                            <h3 class="mb-0">{{ $totalSent ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="ri-mail-send-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Esta Semana</h6>
                            <h3 class="mb-0">{{ $thisWeek ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="ri-calendar-week-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-blue text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Usuarios Activos</h6>
                            <h3 class="mb-0">{{ $activeUsers ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="ri-user-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Plantillas</h6>
                            <h3 class="mb-0">1</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="ri-file-text-line fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Notifications History -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="ri-history-line me-2"></i>Historial de Envíos
            </h5>
        </div>
        <div class="card-body">
            @if (isset($emailNotifications) && count($emailNotifications) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Asunto</th>
                                <th>Destinatarios</th>
                                <th>Estado</th>
                                <th>Fecha de Envío</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($emailNotifications as $notification)
                                <tr>
                                    <td>
                                        <strong>{{ $notification->subject ?? 'Sin asunto' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($notification->body ?? 'Sin contenido', 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $notification->recipient_count ?? 0 }} usuarios</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="ri-check-line me-1"></i>Enviado
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ isset($notification->created_at) ? $notification->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"
                                                onclick="viewDetails('{{ $notification->id ?? 0 }}')">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(isset($emailNotifications) && method_exists($emailNotifications, 'links'))
                    <div class="mt-3">
                        {{ $emailNotifications->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="ri-mail-line fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No se han enviado notificaciones por email aún</h5>
                    <p class="text-muted">Comienza enviando tu primera notificación por email a los usuarios.</p>
                    <a href="{{ route('admin.email-notifications.create') }}" class="btn btn-primary">
                        <i class="ri-mail-send-line me-2"></i>Enviar Primera Notificación
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal for viewing details -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Notificación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
function viewDetails(id) {
    // This would load notification details via AJAX
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    document.getElementById('modalContent').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
    modal.show();
    
    // Here you would make an AJAX call to load the details
    // For now, just show a placeholder
    setTimeout(() => {
        document.getElementById('modalContent').innerHTML = `
            <div class="alert alert-info">
                <i class="ri-information-line me-2"></i>
                Funcionalidad de detalles en desarrollo. ID: ${id}
            </div>
        `;
    }, 500);
}
</script>
@endpush
@endsection
