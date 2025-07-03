@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Facultativo Dashboard')
@section('content')
<div class="container py-4">
    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="ri-calendar-line fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Citas de Hoy</h6>
                            <h3 class="mb-0">{{ $solicitudesPendientes ?? 0 }}</h3>
                            <a href="/facultativo/citas" class="text-white text-decoration-underline small">Ver citas</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="ri-calendar-event-line fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Citas a confirmar</h6>
                            <h3 class="mb-0">{{ $eventosMes ?? 0 }}</h3>
                            <a href="/facultativo/citas/pendientes" class="text-white text-decoration-underline small">Ver citas a confirmar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="ri-user-line fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1 ">Siguiente cita</h6>
                            <h6 class="mb-0">nombre del paciente</h6>
                            <!-- link a cita siguiente -->
                            <a href="/facultativo/pacientes" class="text-white text-decoration-underline small">Ver siguiente cita</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <hr>
    <!-- max 10 campos -->
    <h3 class="mb-4 text-warning"><i class="fas fa-calendar-alt"></i> Citas a Confirmar</h3>
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
                    @forelse($citasPendientes as $cita)
                    <tr>
                        <td class="text-center align-middle">{{ $cita->id }}</td>
                        <td class="text-center align-middle">
                            {{ $cita->alumno ? $cita->alumno->name : 'Sin paciente' }}
                        </td>
                        <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('Y-m-d') : '-' }}</td>
                        <td class="text-center align-middle">{{ $cita->fecha_propuesta ? $cita->fecha_propuesta->format('H:i') : '-' }}</td>
                        <td class="text-center align-middle">{{ $cita->duracionFormateada }}</td>
                        <td class="text-center align-middle">
                            <span class="badge bg-warning">pendiente</span>
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ url('/facultativo/cita/'.$cita->id) }}" class="btn btn-primary btn-sm mr-2">
                                <i class="ri-search-line"></i> Detalles
                            </a>
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm mr-2">
                                <i class="fa-solid fa-xmark"></i></i> Rechazada
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
        <a class="btn btn-warning mt-3" href="/facultativo/citas/pendientes">Ver más</a>
    </div>
</div>
@endsection