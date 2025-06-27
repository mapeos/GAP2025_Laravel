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
</div>
@endsection