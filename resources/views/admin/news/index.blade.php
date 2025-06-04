@extends('template.base')

@section('title', 'Listado de Noticias')
@section('title-sidebar', 'Noticias')
@section('title-page', 'Listado de Noticias')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">News</a></li>
<li class="breadcrumb-item active">Index News</li>
@endsection

@section('content')
<div class="container">

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    <h1 class="mb-4">Listado de Noticias</h1>

    <div class="mb-3">
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">Crear Nueva Noticia</a>
    </div>

    <table class="table table-striped align-middle table-hover table-responsive">
        <thead>
            <tr>
                <th>Título</th>
                <th style="width: 200px;">Categorías</th>
                <th>Autor</th>
                <th>Publicada</th>
                <th>Modificada</th>
                <th>Eliminada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($news as $item)
            <tr @if ($item->trashed()) class="table-danger" @endif>
                <td>{{ $item->titulo }}</td>
                <td style="white-space: nowrap; overflow-x: auto;">
                    @if ($item->categorias->isNotEmpty())
                    @foreach ($item->categorias as $categoria)
                    <span class="badge bg-info text-dark">{{ $categoria->nombre }}</span>
                    @endforeach
                    @else
                    <span class="text-muted">Pendiente</span>
                    @endif
                </td>
                <td>{{ $item->autor ?? 'Sin autor' }}</td>
                <td>{{ $item->fecha_publicacion->format('d/m/Y H:i') }}</td>
                <td>{{ $item->updated_at->format('d/m/Y H:i') }}</td>

                <td>
                    @if ($item->trashed())
                    {{ $item->deleted_at->format('d/m/Y H:i') }}
                    @else
                    <span class="text-muted">Activa</span>
                    @endif
                </td>

                <td>
                    @if ($item->trashed())
                    <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-warning btn-sm">Editar</a>

                    <form action="{{ route('admin.news.restore', $item->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm">Publicar</button>
                    </form>
                    @else
                    <a href="{{ route('admin.news.show', $item) }}" class="btn btn-info btn-sm">Ver</a>
                    <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-warning btn-sm">Editar</a>

                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta noticia?')">Eliminar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay noticias registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>
@endsection