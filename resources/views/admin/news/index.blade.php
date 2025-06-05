@extends('template.base-admin')

@section('title', 'Listado de Noticias')
@section('title-sidebar', 'Noticias')
@section('title-page', 'Listado de Noticias')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">News</a></li>
<li class="breadcrumb-item active">Index News</li>
@endsection

@section('content')
<div class="container">

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    @include('template.partials.alerts')

    <h1 class="mb-4">Listado de Noticias</h1>

    <div class="mb-3">
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">Crear Nueva Noticia</a>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary">Ir a Categorías</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark">Ir al Dashboard</a>

    </div>

    <table class="table align-middle table-responsive">
        <thead>
            <tr>
                <th>Título</th>
                <th style="width: 200px;">Categorías</th>
                <th>Autor</th>
                <th>Publicada</th>
                <th>Modificada</th>
                <th>Eliminada</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($news as $item)
            <tr @if ($item->trashed()) class="table-danger" @endif>
                <td>
                    {{ $item->titulo }}
                    @if ($item->trashed())
                    <i class="ri-alert-line text-danger" title="Noticia eliminada"></i>
                    @endif
                </td>
                <td style="white-space: normal; overflow-wrap: break-word;">
                    @if ($item->categorias->isNotEmpty())
                    @foreach ($item->categorias as $categoria)
                    <span class="badge bg-info text-dark me-1 mb-1">{{ $categoria->nombre }}</span>
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
                    <span class="text-muted">-</span>
                    @endif
                </td>

                <td>
                    @if ($item->trashed())
                    <span class="badge bg-danger">Dada de baja</span>
                    @else
                    <span class="badge bg-success">Publicada</span>
                    @endif
                </td>

                <td>
                    <div style="display: flex; gap: 0.3rem; flex-wrap: nowrap; white-space: nowrap;">
                        @if ($item->trashed())
                        <a href="{{ route('admin.news.show', $item) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-warning btn-sm">Editar</a>

                        <form action="{{ route('admin.news.restore', $item->id) }}" method="POST" style="display:inline-flex; margin:0;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success btn-sm">Publicar</button>
                        </form>
                        @else
                        <a href="{{ route('admin.news.show', $item) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-warning btn-sm">Editar</a>

                        <form action="{{ route('admin.news.destroy', $item) }}" method="POST" style="display:inline-flex; margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta noticia?')">Eliminar</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No hay noticias registradas.</td>
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