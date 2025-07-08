@extends('template.base-alumno')

@section('title', 'Detalle del Curso')
@section('title-page', $curso->titulo)

@section('breadcrumbs')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0 mb-3">
        <li class="breadcrumb-item"><a href="{{ route('alumno.home') }}"><i class="ri-home-2-line"></i> Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $curso->titulo }}</li>
    </ol>
</nav>
@endsection

@section('content')
    <div class="container my-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="ri-book-open-line me-2"></i> {{ $curso->titulo }}</h4>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Descripción:</strong> {{ $curso->descripcion }}</p>
                <p class="mb-2"><strong>Fechas:</strong> {{ $curso->fechaInicio }} - {{ $curso->fechaFin }}</p>
                <p class="mb-2"><strong>Plazas:</strong> {{ $curso->plazas }}</p>
                <p class="mb-2"><strong>Estado:</strong> <span class="badge bg-{{ $curso->estado == 'activo' ? 'success' : 'secondary' }}">{{ ucfirst($curso->estado) }}</span></p>

                {{-- Sección de diploma --}}
                <div class="mt-4">
                    <h5><i class="ri-award-line me-2"></i> Diploma del curso</h5>
                    @if($diploma)
                        <a href="{{ $diploma->url_pdf }}" target="_blank" class="btn btn-success">
                            <i class="ri-download-line me-1"></i> Descargar mi diploma
                        </a>
                    @else
                        <button id="solicitar-diploma-btn" class="btn btn-primary">
                            <i class="ri-file-text-line me-1"></i> Solicitar diploma
                        </button>
                    @endif
                </div>
                <a href="{{ route('alumno.cursos.inscribir', $curso->id) }}" class="btn btn-success mt-3">
                    <i class="ri-user-add-line me-1"></i> Solicitar inscripción en este curso
                </a>
                <a href="{{ route('alumno.home') }}" class="btn btn-secondary mt-3 ms-2">
                    <i class="ri-arrow-left-line me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('solicitar-diploma-btn');
    if (btn) {
        btn.addEventListener('click', function() {
            if (!confirm('¿Estás seguro de que quieres solicitar tu diploma?')) return;
            btn.disabled = true;
            btn.innerHTML = '<i class="ri-loader-4-line"></i> Generando...';
            fetch(`/admin/cursos/{{ $curso->id }}/diploma/participante/{{ auth()->user()->persona->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('¡Diploma generado correctamente!');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-file-text-line me-1"></i> Solicitar diploma';
                }
            })
            .catch(() => {
                alert('Error al generar el diploma. Inténtalo de nuevo.');
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-file-text-line me-1"></i> Solicitar diploma';
            });
        });
    }
});
</script>
@endpush
