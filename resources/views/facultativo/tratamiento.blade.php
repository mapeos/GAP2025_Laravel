@php
$tratamiento = (object)[
    'nombre' => 'Nombre de ejemplo',
    'descripcion' => 'Descripción de ejemplo del tratamiento.',
    'especialidad' => (object)[
        'nombre' => 'Especialidad de ejemplo'
    ],
    'duracion_formateada' => '1 hora',
    'duracion_minutos' => 60,
    'costo_formateado' => '€50.00',
    'activo' => true,
];
@endphp

@push('js')
@push('css')
@extends('template.base-facultativo')
@section('title', 'Facultativo Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Tratamiento')
@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-success flex items-center gap-2">
        <i class="ri-stethoscope-line"></i>
        <span>Detalles del Tratamiento</span>
    </h3>
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="w-25">Nombre</th>
                        <td>{{ $tratamiento->nombre ?? 'No disponible' }}</td>
                    </tr>
                    <tr>
                        <th>Descripción</th>
                        <td>{{ $tratamiento->descripcion ?? 'No disponible' }}</td>
                    </tr>
                    <tr>
                        <th>Especialidad</th>
                        <td>{{ $tratamiento->especialidad ? $tratamiento->especialidad->nombre : 'No disponible' }}</td>
                    </tr>
                    <tr>
                        <th>Duración</th>
                        <td>
                            @if(isset($tratamiento->duracion_formateada))
                                {{ $tratamiento->duracion_formateada }}
                            @elseif(isset($tratamiento->duracion_minutos))
                                {{ $tratamiento->duracion_minutos . ' minutos' }}
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Costo</th>
                        <td>
                            @if(isset($tratamiento->costo_formateado))
                                {{ $tratamiento->costo_formateado }}
                            @elseif(isset($tratamiento->costo))
                                €{{ number_format($tratamiento->costo, 2) }}
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Activo</th>
                        <td>
                            @if(isset($tratamiento->activo) && $tratamiento->activo)
                                <span class="badge bg-success">Sí</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <a href="/facultativo/tratamientos" class="btn btn-outline-success">
        <i class="ri-arrow-left-line"></i> Volver a la lista de tratamientos
    </a>
</div>
@endsection