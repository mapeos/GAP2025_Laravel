@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Tratamientos')
@section('content')
<div class="container">
    <h3 class="mb-4 text-success"><i class="fas fa-calendar-alt"></i> Tratamientos Médicos</h3>
    <a href="{{ route('facultativo.tratamiento.new') }}" class="btn btn-outline-success mt-3 mb-3"><i class="ri-add-line text-lg"></i> Nuevo Tratamiento</a>
    <div class="card shadow-sm mb-5">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center align-middle table-bordered">
                <thead class="thead-light bg-success text-white">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Descripción</th>
                        <th class="text-center">Especialidad</th>
                        <th class="text-center">Duración</th>
                        <th class="text-center">Costo</th>
                        <th class="text-center">Activo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tratamientos as $tratamiento)
                        <tr>
                            <td class="text-center align-middle">{{ $tratamiento->id }}</td>
                            <td class="text-center align-middle">{{ $tratamiento->nombre }}</td>
                            <td class="text-center align-middle">{{ $tratamiento->descripcion }}</td>
                            <td class="text-center align-middle">{{ $tratamiento->especialidad ? $tratamiento->especialidad->nombre : '-' }}</td>
                            <td class="text-center align-middle">{{ $tratamiento->duracion_minutos ? $tratamiento->duracion_minutos . ' minutos' : '-' }}</td>
                            <td class="text-center align-middle">€{{ number_format($tratamiento->costo, 2) }}</td>
                            <td class="text-center align-middle">
                                @if(isset($tratamiento->activo) && $tratamiento->activo)
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <a href="{{ route('facultativo.tratamiento', $tratamiento->id) }}" class="btn btn-primary btn-sm w-100 mb-1">
                                        <i class="ri-search-line"></i> Detalles
                                    </a>
                                    <a href="{{ route('facultativo.tratamiento.edit', $tratamiento->id) }}" class="btn btn-warning btn-sm w-100 mb-1">
                                        <i class="ri-edit-line"></i> Editar
                                    </a>
                                    <form action="{{ route('facultativo.tratamiento.destroy', $tratamiento->id) }}" method="POST" class="w-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                            <i class="ri-delete-bin-line"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay tratamientos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <button class="btn btn-outline-success mt-3">Ver más</button>
    </div>
</div>
@endsection