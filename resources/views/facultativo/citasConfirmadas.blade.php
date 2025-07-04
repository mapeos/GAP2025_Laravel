@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Citas Confirmadas')
@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-success"><i class="fas fa-calendar-alt"></i> Citas Confirmadas</h3>
    <div class="card shadow-sm mb-5">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0 text-center align-middle">
                <thead class="">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Paciente</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center">Duración</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas as $cita)
                        <tr>
                            <td class="text-center align-middle">{{ $cita->id }}</td>
                            <td class="text-center align-middle">
                                {{ $cita->alumno ? $cita->alumno->name : 'Sin paciente' }}
                            </td>
                            <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('Y-m-d') : '-' }}</td>
                            <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('H:i') : '-' }}</td>
                            <td class="text-center align-middle">{{ $cita->duracionFormateada }}</td>
                            <td class="text-center align-middle">
                                <span class="badge bg-success">Confirmada</span>
                            </td>
                            <td class="text-center align-middle">
                                <a href="{{ url('/facultativo/cita/'.$cita->id) }}" class="btn btn-primary btn-sm mr-2">
                                    <i class="ri-search-line"></i> Detalles
                                </a>
                                <button class="btn btn-success btn-sm mr-2">
                                    <i class="fas fa-check"></i> Finalizar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay citas confirmadas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <button class="btn btn-outline-success mt-3">Ver más</button>
    </div>
</div>
@endsection