@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Paciente')
@section('content')
<div class="container py-4">
    <h3 class="mb-6 text-success flex items-center gap-2">
        <i class="ri-user-line"></i> 
        <span>Detalles del Paciente</span>
    </h3>
    <hr>
    <h6>nombre de paciente</h6>
    <h3>{{ $paciente->nombre ?? 'Nombre no disponible' }}</h3>
    <hr>
    <h6>Tel</h6>
    <h3>{{ $paciente->tfno ?? 'telefono no disponible' }}</h3>
    <hr>
    <h6>Email</h6>
    <h3>{{ $paciente->email ?? 'email no disponible' }}</h3>
    <hr>
    <h6>Dir</h6>
    <h3>{{ $paciente->dir ?? 'Direccion no disponible' }}</h3>
    <hr>
    <h3 class="mb-4 text-success"><i class="fas fa-calendar-alt"></i> Citas</h3>
    <div class="card shadow-sm mb-5">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center align-middle">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Paciente</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center">duracion estimada</th>
                        <th class="text-center">actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td class="text-center align-middle">Juan Pérez</td>
                        <td class="text-center align-middle">2024-06-10</td>
                        <td class="text-center align-middle">09:00</td>
                        <td class="text-center align-middle">5</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button class="btn btn-primary btn-sm">
                                <i class="ri-search-line"></i> Mas detalles...
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">2</td>
                        <td class="text-center align-middle">María López</td>
                        <td class="text-center align-middle">2024-06-11</td>
                        <td class="text-center align-middle">10:30</td>
                        <td class="text-center align-middle">30</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button class="btn btn-primary btn-sm">
                                <i class="ri-search-line"></i> Mas detalles...
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">3</td>
                        <td class="text-center align-middle">Carlos Ruiz</td>
                        <td class="text-center align-middle">2024-06-12</td>
                        <td class="text-center align-middle">11:00</td>
                        <td class="text-center align-middle">10</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button class="btn btn-primary btn-sm">
                                <i class="ri-search-line"></i> Mas detalles...
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <h3 class="mb-4 text-warning"><i class="fas fa-calendar-alt"></i> Tratamientos</h3>
    <div class="card shadow-sm mb-5">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 text-center align-middle">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Paciente</th>
                        <th class="text-center">descripcion</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center">duracion estimada</th>
                        <th class="text-center">actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td class="text-center align-middle">Juan Pérez</td>
                        <td class="text-center align-middle">2024-06-10</td>
                        <td class="text-center align-middle">09:00</td>
                        <td class="text-center align-middle">5</td>
                        <td class="text-center align-middle">
                            <button class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Confirmar
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button class="btn btn-primary btn-sm">
                                <i class="ri-search-line"></i> Mas detalles...
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection