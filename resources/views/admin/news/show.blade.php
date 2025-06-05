@extends('template.base')

@section('title', 'Detalle de Noticia')
@section('title-sidebar', 'Noticias')
@section('title-page', 'Detalle de Noticia')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">News</a></li>
<li class="breadcrumb-item active">Show News</li>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $news->titulo }}</h1>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    <p><strong>Categorías:</strong>
        @if ($news->categorias->isNotEmpty())
        @foreach ($news->categorias as $categoria)
        <span class="badge bg-primary">{{ $categoria->nombre }}</span>
        @endforeach
        @else
        <span class="text-muted">Pendiente</span>
        @endif
    </p>

    <p><strong>Autor:</strong> {{ $news->autor ?? 'No asignado' }}</p>

    <div class="mb-3">
        <strong>Contenido:</strong>
        <p>{{ $news->contenido }}</p>
    </div>

    <p><strong>Fecha de Publicación:</strong> {{ $news->fecha_publicacion->format('d/m/Y H:i') }}</p>

    <a href="{{ route('admin.news.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
</div>
@endsection