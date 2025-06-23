@extends('template.base')

@section('title', 'Cursos')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div class="flex-grow-1 text-center">
            <span class="fw-bold fs-3">Cursos</span>
        </div>
        <a href="{{ route('admin.cursos.create') }}"
            class="btn btn-warning btn-lg fw-bold shadow ms-3"
            style="padding: 0.75rem 2rem; font-size: 1.2rem;"
            title="Crear curso">
            <i class="fa fa-plus me-1"></i> Crear curso
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <!-- <th>Descripción</th> -->
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Plazas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cursos as $curso)
                <tr>
                    <td>{{ $curso->id }}</td>
                    <td>{{ $curso->titulo }}</td>
                    <!-- <td>{{ $curso->descripcion }}</td> -->
                    <td>{{ $curso->fechaInicio }}</td>
                    <td>{{ $curso->fechaFin }}</td>
                    <td>{{ $curso->plazas }}</td>
                    <td>{{ $curso->estado }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.cursos.edit', $curso->id) }}"
                            class="btn btn-warning btn-sm me-1"
                            title="Modificar">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.cursos.destroy', $curso->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm me-1" title="Eliminar"
                                onclick="return confirm('¿Seguro que deseas eliminar este curso?')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.cursos.show', $curso->id) }}"
                            class="btn btn-info btn-sm"
                            title="Más información">
                            <i class="fa fa-info-circle"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">No hay cursos disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection