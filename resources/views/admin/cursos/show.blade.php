@extends('template.base')

@inject('storage', 'Illuminate\Support\Facades\Storage')

@section('title', 'Detalles del Curso')

@section('content')
    {{-- Mensajes flash --}}
@if (session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="ri-checkbox-circle-fill text-success me-2 fs-4"></i>
            <div>{{ session('success') }}</div>
</div>
@endif

@if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="ri-close-circle-fill text-danger me-2 fs-4"></i>
            <div>
                <strong>Errores encontrados:</strong>
                <ul class="mb-0 mt-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
    </div>
@endif

    {{-- Alertas de estado --}}
    @include('admin.cursos.parts.status-alerts', ['curso' => $curso])

    {{-- Estadísticas del curso --}}
    @include('admin.cursos.parts.course-stats', ['curso' => $curso])

    {{-- Detalles completos del curso --}}
    @include('admin.cursos.parts.course-details', ['curso' => $curso])

    {{-- Sección de gestión: Participantes y Temario --}}
    <div class="row">
        <div class="col-md-6">
            @include('admin.cursos.parts.participante', ['curso' => $curso])
        </div>
        <div class="col-md-6">
            @include('admin.cursos.parts.temario', ['curso' => $curso])
        </div>
    </div>
@endsection