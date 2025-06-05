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

    {{-- Mensajes de sesión y errores --}}
    @include('template.partials.alerts')

    <div class="mb-3">
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary">Crear Nueva Categoría</a>
    </div>

    <table class="table align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categorias as $categoria)
            <tr>
                <td>{{ $categoria->nombre }}</td>
                <td>{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
                <td>
                    <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn btn-warning btn-sm">Editar</a>

                    <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta categoría?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">No hay categorías registradas.</td>
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