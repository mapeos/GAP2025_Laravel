@extends('template.base')

@section('title', 'Listado de Categorías')
@section('title-sidebar', 'Categorías')
@section('title-page', 'Listado de Categorías')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="#">Categorías</a></li>
<li class="breadcrumb-item active">Index Categorías</li>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Categorías</h1>

    {{-- Mensajes flash (éxito, error, info, warning y validaciones) --}}
    @include('template.partials.alerts')

    <div class="mb-3">
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary">Crear Nueva Categoría</a>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Ir a Noticias</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark">Ir al Dashboard</a>

    </div>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categorias as $categoria)
            <tr @if ($categoria->trashed()) class="table-danger" @endif>
                <td>
                    {{ $categoria->nombre }}
                    @if ($categoria->trashed())
                    <i class="ri-alert-line text-danger" title="Categoría eliminada"></i>
                    @endif
                </td>
                <td>{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
                <td>
                    @if ($categoria->trashed())
                    <span class="badge bg-danger">Eliminada</span>
                    @else
                    <span class="badge bg-success">Activa</span>
                    @endif
                </td>
                <td>
                    @if ($categoria->trashed())
                    <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('admin.categorias.restore', $categoria->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm">Publicar</button>
                    </form>
                    @else
                    <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn btn-warning btn-sm">Editar</a>
                    <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No hay categorías registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


    <!-- Paginación si usas -->
    {{-- <div class="d-flex justify-content-center">
        {{ $categorias->links() }}
</div> --}}
</div>
@endsection