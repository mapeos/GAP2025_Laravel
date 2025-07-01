@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Citas Pendientes')
@section('content')
<div class="container py-4">
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