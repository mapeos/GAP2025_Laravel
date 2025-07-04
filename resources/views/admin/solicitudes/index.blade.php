@extends('template.base')

@section('title', 'Solicitudes de Inscripción')
@section('title-page', 'Gestión de Solicitudes de Inscripción')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="ri-user-add-line me-2"></i> Solicitudes de Inscripción</h4>
        </div>
        <div class="card-body" id="tabla-solicitudes-container">
            @include('admin.solicitudes._tabla_paginada', ['solicitudes' => $solicitudes])
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="/admin/js/admin-solicitudes-pagination.js"></script>
@endpush
