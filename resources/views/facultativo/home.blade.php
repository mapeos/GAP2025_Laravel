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
                            <a href="/facultativo/citas" class="text-white text-decoration-underline small">Ver citas a confirmar</a>
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
    <!-- tablas de citas -->
    <h3 class="mb-4 text-success"><i class="fas fa-calendar-alt"></i> Citas de Hoy</h3>
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
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td class="text-center align-middle">Juan Pérez</td>
                        <td class="text-center align-middle">2024-06-10</td>
                        <td class="text-center align-middle">09:00</td>
                        <td class="text-center align-middle">30 min</td>
                        <td class="text-center align-middle"><span class="badge bg-success">Confirmada</span></td>
                        <td class="text-center align-middle">
                            <a href="/facultativo/cita/1" class="btn btn-primary btn-sm mr-2">
                                <i class="ri-search-line"></i> Detalles
                            </a>
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Finalizar
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">2</td>
                        <td class="text-center align-middle">María López</td>
                        <td class="text-center align-middle">2024-06-11</td>
                        <td class="text-center align-middle">10:30</td>
                        <td class="text-center align-middle">45 min</td>
                        <td class="text-center align-middle"><span class="badge bg-warning text-dark">Pendiente</span></td>
                        <td class="text-center align-middle">
                            <a href="/facultativo/cita/2" class="btn btn-primary btn-sm mr-2">
                                <i class="ri-search-line"></i> Detalles
                            </a>
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">3</td>
                        <td class="text-center align-middle">Carlos Ruiz</td>
                        <td class="text-center align-middle">2024-06-12</td>
                        <td class="text-center align-middle">11:00</td>
                        <td class="text-center align-middle">60 min</td>
                        <td class="text-center align-middle"><span class="badge bg-danger">Cancelada</span></td>
                        <td class="text-center align-middle">
                            <a href="/facultativo/cita/3" class="btn btn-primary btn-sm mr-2">
                                <i class="ri-search-line"></i> Detalles
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-outline-success mt-3">Ver más</button>
    </div>

    <hr>
    <!-- max 10 campos -->
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
                    <tr>
                        <td class="text-center align-middle">4</td>
                        <td class="text-center align-middle">Ana Torres</td>
                        <td class="text-center align-middle">2024-06-13</td>
                        <td class="text-center align-middle">12:00</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm mr-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <a href="/facultativo/cita/4" class="btn btn-primary btn-sm">
                                <i class="ri-search-line"></i> Detalles
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">5</td>
                        <td class="text-center align-middle">Pedro Gómez</td>
                        <td class="text-center align-middle">2024-06-14</td>
                        <td class="text-center align-middle">13:30</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm mr-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <a href="/facultativo/cita/5" class="btn btn-primary btn-sm">
                                <i class="ri-search-line"></i> Detalles
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-outline-warning mt-3">Ver más</button>
    </div>
</div>
@endsection