@extends('template.base')

@inject('storage', 'Illuminate\Support\Facades\Storage')

@section('title', 'Detalles del Curso')

@section('content')
    {{-- Tarjeta Info (100% ancho) --}}
    @include('admin.cursos.parts.info', ['curso' => $curso])

    {{-- Dos tarjetas al 50%: Participante y Temario --}}
    <div class="row">
        <div class="col-md-6">
            @include('admin.cursos.parts.participante', ['curso' => $curso])
        </div>
        <div class="col-md-6">
            @include('admin.cursos.parts.temario', ['curso' => $curso])
        </div>
    </div>

    <a href="{{ route('admin.cursos.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
@endsection