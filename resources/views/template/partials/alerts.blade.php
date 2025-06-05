{{-- Success Message --}}
@if (session('success'))
<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
    <i class="ri-checkbox-circle-fill text-success me-2 fs-4"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
@endif

{{-- Error Message --}}
@if (session('error'))
<div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
    <i class="ri-close-circle-fill text-danger me-2 fs-4"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
@endif

{{-- Warning Message --}}
@if (session('warning'))
<div class="alert alert-warning d-flex align-items-center alert-dismissible fade show" role="alert">
    <i class="ri-alert-line text-warning me-2 fs-4"></i>
    <div>{{ session('warning') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
@endif

{{-- Info Message --}}
@if (session('info'))
<div class="alert alert-info d-flex align-items-center alert-dismissible fade show" role="alert">
    <i class="ri-information-line text-info me-2 fs-4"></i>
    <div>{{ session('info') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
@endif

{{-- Validation Errors --}}
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-start">
        <i class="ri-error-warning-line text-danger me-2 fs-4 mt-1"></i>
        <div>
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
@endif