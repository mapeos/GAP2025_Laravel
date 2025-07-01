@extends('template.base')

@section('title', 'Cursos Activos - Inscripción')
@section('title-sidebar', 'Inscripciones')
@section('title-page', 'Cursos Activos para Inscripción')

@section('breadcrumb')
    <li class="breadcrumb-item active">Cursos Activos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-book-open-line me-2"></i>
                    Cursos Activos Disponibles
                    <span class="badge bg-primary ms-2">{{ $cursos->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($cursos->count() > 0)
                    <div class="row">
                        @foreach($cursos as $curso)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h6 class="card-title text-primary mb-0">{{ $curso->titulo }}</h6>
                                        <span class="badge bg-success">Activo</span>
                                    </div>
                                    
                                    <p class="text-muted small mb-3">
                                        {{ Str::limit($curso->descripcion, 100) }}
                                    </p>
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <h6 class="text-primary mb-0">{{ $curso->plazas }}</h6>
                                            <small class="text-muted">Plazas</small>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="text-success mb-0">{{ $curso->inscritos_count }}</h6>
                                            <small class="text-muted">Inscritos</small>
                                        </div>
                                        <div class="col-4">
                                            <h6 class="text-warning mb-0">{{ $curso->plazas - $curso->inscritos_count }}</h6>
                                            <small class="text-muted">Disponibles</small>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mb-3" style="height: 6px;">
                                        @php
                                            $porcentaje = $curso->plazas > 0 ? ($curso->inscritos_count / $curso->plazas) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-success progress-width" data-width="{{ $porcentaje }}"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted">
                                            <i class="ri-calendar-line me-1"></i>
                                            {{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}
                                        </small>
                                        <small class="text-muted">{{ number_format($porcentaje, 1) }}% ocupado</small>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.inscripciones.cursos.inscribir.form', $curso->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="ri-user-add-line me-1"></i>
                                            Inscribir Personas
                                        </a>
                                        <a href="{{ route('admin.inscripciones.cursos.inscritos', $curso->id) }}" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="ri-team-line me-1"></i>
                                            Ver Inscritos
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="ri-book-open-line text-muted fs-1 mb-3"></i>
                        <h5 class="text-muted">No hay cursos activos</h5>
                        <p class="text-muted">No hay cursos activos disponibles para inscripción en este momento.</p>
                        <a href="{{ route('admin.cursos.create') }}" class="btn btn-primary">
                            <i class="ri-add-line me-2"></i>
                            Crear Nuevo Curso
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.card {
    transition: transform 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.progress-width');
    progressBars.forEach(function(bar) {
        const width = bar.getAttribute('data-width');
        bar.style.width = width + '%';
    });
});
</script>
@endpush 