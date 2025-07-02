@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('content')
<div class="container py-4">
    <a href="/facultativo/cita/new" class="btn btn-outline-success mb-3"><i class="ri-add-line text-lg"></i> Nueva cita</a>
    <h3 class="mb-4 text-success flex items-center gap-2">
        <i class="fas fa-calendar-alt"></i>
        <span>Citas Confirmadas</span>
    </h3>
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
                    @forelse($citasConfirmadas as $cita)
                    <tr>
                        <td class="text-center align-middle">{{ $cita->id }}</td>
                        <td class="text-center align-middle">{{ $cita->alumno ? $cita->alumno->name : 'Sin paciente' }}</td>
                        <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('Y-m-d') : '-' }}</td>
                        <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $cita->duracionFormateada }}</td>
                        <td class="text-center align-middle"><span class="badge bg-success">Confirmada</span></td>
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
        <a class="btn btn-success mt-3" href="/facultativos/citas/confirmadas">Ver más</a>
    </div>

    <hr>
    <h3 class="mb-4 text-warning flex items-center gap-2">
        <i class="fas fa-clock"></i>
        <span>Citas pendientes a confirmar</span>
    </h3>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0 text-center align-middle">
                <thead class="">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Paciente</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citasPendientes as $cita)
                    <tr>
                        <td class="text-center align-middle">{{ $cita->id }}</td>
                        <td class="text-center align-middle">{{ $cita->alumno ? $cita->alumno->name : 'Sin paciente' }}</td>
                        <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('Y-m-d') : '-' }}</td>
                        <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm mr-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <a href="{{ url('/facultativo/cita/'.$cita->id) }}" class="btn btn-primary btn-sm">
                                <i class="ri-search-line"></i> Detalles
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No hay citas pendientes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a class="btn btn-warning mt-3" href="/facultativo/citas/pendientes">Ver más</a>
    </div>
</div>
@endsection