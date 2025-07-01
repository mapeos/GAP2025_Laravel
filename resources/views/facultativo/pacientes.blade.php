@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-success"><i class="ri-user-line"></i>Pacientes</h3>
    <form method="GET" action="/*" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar paciente ..." value="{{ request('search') }}">
            <button class="btn btn-outline-success" type="submit">
                <i class="ri-search-line"></i> Buscar
            </button>
        </div>
    </form>
    <h3 class="mb-4 text-success"><i class="ri-user-heart-line"></i> Lista de Pacientes</h3>
    <div class="card shadow-sm mb-5">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0 text-center align-middle">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Edad</th>
                        <th class="text-center">Género</th>
                        <th class="text-center">Teléfono</th>
                        <th class="text-center">Correo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td class="text-center align-middle">Juan Pérez</td>
                        <td class="text-center align-middle">34</td>
                        <td class="text-center align-middle">Masculino</td>
                        <td class="text-center align-middle">555-1234</td>
                        <td class="text-center align-middle">juan.perez@email.com</td>
                        <td class="text-center align-middle">
                            <a href="/facultativo/paciente" class="btn btn-primary btn-sm mr-2">
                                <i class="ri-search-line"></i> Ver
                            </a>
                            <a href="/facultativo/paciente" class="btn btn-warning btn-sm mr-2">
                                <i class="ri-edit-line"></i> Editar
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">2</td>
                        <td class="text-center align-middle">María López</td>
                        <td class="text-center align-middle">28</td>
                        <td class="text-center align-middle">Femenino</td>
                        <td class="text-center align-middle">555-5678</td>
                        <td class="text-center align-middle">maria.lopez@email.com</td>
                        <td class="text-center align-middle">
                            <a href="/facultativo/paciente" class="btn btn-primary btn-sm mr-2">
                                <i class="ri-search-line"></i> Ver
                            </a>
                            <a href="/facultativo/paciente" class="btn btn-warning btn-sm mr-2">
                                <i class="ri-edit-line"></i> Editar
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">3</td>
                        <td class="text-center align-middle">Carlos Ruiz</td>
                        <td class="text-center align-middle">45</td>
                        <td class="text-center align-middle">Masculino</td>
                        <td class="text-center align-middle">555-9012</td>
                        <td class="text-center align-middle">carlos.ruiz@email.com</td>
                        <td class="text-center align-middle">
                            <a href="/facultativo/paciente" class="btn btn-primary btn-sm mr-2">
                                <i class="ri-search-line"></i> Ver
                            </a>
                            <a href="/facultativo/paciente" class="btn btn-warning btn-sm mr-2">
                                <i class="ri-edit-line"></i> Editar
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