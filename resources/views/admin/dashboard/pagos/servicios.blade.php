@extends('template.base')
@section('title', 'Resumen de Servicios')
@section('title-sidebar', auth()->user()?->name ?? 'Dashboard Admin')
@section('title-page', 'Resumen de Servicios')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Servicios</li>
@endsection

@section('content')
    <style>
        .servicios-card {
            max-width: 1200px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 16px 0 #0001;
            padding: 2rem 2.5rem 2.5rem 2.5rem;
        }
        .servicios-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
        }
        .servicios-table th {
            background: #6366f1;
            color: #fff;
            font-weight: 600;
            padding: 0.9rem 0.7rem;
            border: none;
        }
        .servicios-table td {
            padding: 0.8rem 0.7rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            font-size: 1rem;
        }
        .servicios-table tr:nth-child(even) td {
            background: #f3f4f6;
        }
        .servicios-table tr:hover td {
            background: #e0e7ff;
        }
        .badge {
            padding: 0.4em 0.9em;
            border-radius: 12px;
            font-size: 0.95em;
            font-weight: 600;
        }
        .badge.bg-success {
            background: #22c55e;
            color: #fff;
        }
        .badge.bg-warning {
            background: #facc15;
            color: #7c4700;
        }
        .badge.bg-danger {
            background: #ef4444;
            color: #fff;
        }
        .servicios-empty {
            text-align: center;
            color: #64748b;
            font-size: 1.1em;
            padding: 1.5em 0;
        }
        @media (max-width: 900px) {
            .servicios-card { padding: 1rem; }
            .servicios-table th, .servicios-table td { font-size: 0.95em; }
        }
    </style>
    <div class="servicios-card">
        <h5 class="card-title">Resumen de Cursos y Servicios</h5>
        <div class="table-responsive">
            <table class="servicios-table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Estado</th>
                        <th>Precio</th>
                        <th>Inscripciones</th>
                        <th>Plazas</th>
                        <th>Ocupación</th>
                        <th>Total ingresos (€)</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cursos as $curso)
                        @php
                            $inscripciones = $curso->pagos->count();
                            $ingresos = $curso->pagos->sum('importe');
                            $ocupacion = $curso->plazas > 0 ? round(($inscripciones / $curso->plazas) * 100, 1) : 0;
                            $estado = $curso->getEstadoTemporal();
                        @endphp
                        <tr>
                            <td>{{ $curso->titulo }}</td>
                            <td>
                                <span class="badge {{ $estado == 'Finalizado' ? 'bg-danger' : ($estado == 'En Progreso' ? 'bg-warning' : 'bg-success') }}">
                                    {{ $estado }}
                                </span>
                            </td>
                            <td>€{{ number_format($curso->precio, 2) }}</td>
                            <td>{{ $inscripciones }}</td>
                            <td>{{ $curso->plazas }}</td>
                            <td>{{ $ocupacion }}%</td>
                            <td>€{{ number_format($ingresos, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.cursos.show', $curso->id) }}" class="btn btn-sm btn-info">Ver curso</a>
                                <a href="{{ route('admin.cursos.show', $curso->id) }}#pagos" class="btn btn-sm btn-primary" title="Ver usuarios que han pagado">
                                    <i class="ri-user-shared-line"></i> Pagadores
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="servicios-empty">No hay cursos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
