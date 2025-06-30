@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Tratamientos')
@section('content')
<div class="container">
    <h3 class="mb-4 text-success"><i class="fas fa-calendar-alt"></i> Tratamientos Médicos</h3>
    <a href="/facultativo/tratamiento/new" class="btn btn-outline-success mt-3 mb-3"><i class="ri-add-line text-lg"></i> Nuevo Tratamiento</a>
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
                    <tr>
                        <td class="text-center align-middle">1</td>
                        <td class="text-center align-middle">Limpieza Dental</td>
                        <td class="text-center align-middle">Eliminación de placa y sarro</td>
                        <td class="text-center align-middle">Odontología</td>
                        <td class="text-center align-middle">30 minutos</td>
                        <td class="text-center align-middle">€35.00</td>
                        <td class="text-center align-middle"><span class="badge bg-success">Sí</span></td>
                        <td class="text-center align-middle">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <a href="/facultativo/tratamiento" class="btn btn-primary btn-sm w-100 mb-1">
                                    <i class="ri-search-line"></i> Detalles
                                </a>
                                <a class="btn btn-warning btn-sm w-100 mb-1">
                                    <i class="ri-edit-line"></i> Editar
                                </a>
                                <a class="btn btn-danger btn-sm w-100">
                                    <i class="ri-delete-bin-line"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">2</td>
                        <td class="text-center align-middle">Consulta General</td>
                        <td class="text-center align-middle">Revisión médica general</td>
                        <td class="text-center align-middle">Medicina General</td>
                        <td class="text-center align-middle">20 minutos</td>
                        <td class="text-center align-middle">€20.00</td>
                        <td class="text-center align-middle"><span class="badge bg-success">Sí</span></td>
                        <td class="text-center align-middle">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <a href="/facultativo/tratamiento" class="btn btn-primary btn-sm w-100 mb-1">
                                    <i class="ri-search-line"></i> Detalles
                                </a>
                                <a class="btn btn-warning btn-sm w-100 mb-1">
                                    <i class="ri-edit-line"></i> Editar
                                </a>
                                <a class="btn btn-danger btn-sm w-100">
                                    <i class="ri-delete-bin-line"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">3</td>
                        <td class="text-center align-middle">Radiografía</td>
                        <td class="text-center align-middle">Estudio de imagen dental</td>
                        <td class="text-center align-middle">Radiología</td>
                        <td class="text-center align-middle">15 minutos</td>
                        <td class="text-center align-middle">€50.00</td>
                        <td class="text-center align-middle"><span class="badge bg-danger">No</span></td>
                        <td class="text-center align-middle">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <a href="/facultativo/tratamiento" class="btn btn-primary btn-sm w-100 mb-1">
                                    <i class="ri-search-line"></i> Detalles
                                </a>
                                <a class="btn btn-warning btn-sm w-100 mb-1">
                                    <i class="ri-edit-line"></i> Editar
                                </a>
                                <a class="btn btn-danger btn-sm w-100">
                                    <i class="ri-delete-bin-line"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">4</td>
                        <td class="text-center align-middle">Ortodoncia</td>
                        <td class="text-center align-middle">Colocación de brackets</td>
                        <td class="text-center align-middle">Odontología</td>
                        <td class="text-center align-middle">60 minutos</td>
                        <td class="text-center align-middle">€120.00</td>
                        <td class="text-center align-middle"><span class="badge bg-success">Sí</span></td>
                        <td class="text-center align-middle">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <a href="/facultativo/tratamiento" class="btn btn-primary btn-sm w-100 mb-1">
                                    <i class="ri-search-line"></i> Detalles
                                </a>
                                <a class="btn btn-warning btn-sm w-100 mb-1">
                                    <i class="ri-edit-line"></i> Editar
                                </a>
                                <a class="btn btn-danger btn-sm w-100">
                                    <i class="ri-delete-bin-line"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle">5</td>
                        <td class="text-center align-middle">Vacunación</td>
                        <td class="text-center align-middle">Aplicación de vacunas</td>
                        <td class="text-center align-middle">Medicina Preventiva</td>
                        <td class="text-center align-middle">10 minutos</td>
                        <td class="text-center align-middle">€15.00</td>
                        <td class="text-center align-middle"><span class="badge bg-success">Sí</span></td>
                        <td class="text-center align-middle">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <a href="/facultativo/tratamiento" class="btn btn-primary btn-sm w-100 mb-1">
                                    <i class="ri-search-line"></i> Detalles
                                </a>
                                <a class="btn btn-warning btn-sm w-100 mb-1">
                                    <i class="ri-edit-line"></i> Editar
                                </a>
                                <a class="btn btn-danger btn-sm w-100">
                                    <i class="ri-delete-bin-line"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-outline-success mt-3">Ver más</button>
    </div>
</div>
@endsection