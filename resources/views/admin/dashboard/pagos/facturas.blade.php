@extends('template.base')
@section('title', 'Gestión de Facturas')
@section('title-sidebar', auth()->user()?->name ?? 'Dashboard Admin')
@section('title-page', 'Gestión de Facturas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Facturas</li>
@endsection

@section('content')
    <style>
        .facturas-card {
            max-width: 1100px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 16px 0 #0001;
            padding: 2rem 2.5rem 2.5rem 2.5rem;
        }
        .facturas-card h5 {
            font-size: 1.4rem;
            color: #2d3748;
            margin-bottom: 1.2rem;
        }
        .facturas-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
        }
        .facturas-table th {
            background: #6366f1;
            color: #fff;
            font-weight: 600;
            padding: 0.9rem 0.7rem;
            border: none;
        }
        .facturas-table td {
            padding: 0.8rem 0.7rem;
            border-top: 1px solid #e5e7eb;
            background: #fff;
            font-size: 1rem;
        }
        .facturas-table tr:nth-child(even) td {
            background: #f3f4f6;
        }
        .facturas-table tr:hover td {
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
        .btn-sm.btn-info {
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.4em 1.1em;
            font-size: 1em;
            transition: background 0.2s;
        }
        .btn-sm.btn-info:hover {
            background: #4f46e5;
            color: #fff;
        }
        .facturas-empty {
            text-align: center;
            color: #64748b;
            font-size: 1.1em;
            padding: 1.5em 0;
        }
        @media (max-width: 900px) {
            .facturas-card { padding: 1rem; }
            .facturas-table th, .facturas-table td { font-size: 0.95em; }
        }
    </style>
    <div class="facturas-card">
        <h5 class="card-title">Gestión de Facturas</h5>
        <form method="GET" action="" style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
            <div>
                <label for="buscar">Buscar curso:</label><br>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" placeholder="Nombre del curso" style="padding: 0.5em 1em; border-radius: 6px; border: 1px solid #cbd5e1;">
            </div>
            <div>
                <label for="buscar_usuario">Usuario:</label><br>
                <input type="text" name="buscar_usuario" id="buscar_usuario" value="{{ request('buscar_usuario') }}" placeholder="Nombre, email o DNI" style="padding: 0.5em 1em; border-radius: 6px; border: 1px solid #cbd5e1;">
            </div>
            <div>
                <label for="fecha_inicio">Desde:</label><br>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" style="padding: 0.5em 1em; border-radius: 6px; border: 1px solid #cbd5e1;">
            </div>
            <div>
                <label for="fecha_fin">Hasta:</label><br>
                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" style="padding: 0.5em 1em; border-radius: 6px; border: 1px solid #cbd5e1;">
            </div>
            <div>
                <button type="submit" style="background: #6366f1; color: white; padding: 0.6em 1.5em; border: none; border-radius: 6px; font-weight: 600;">Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="facturas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Tipo de usuario</th>
                        <th>Curso</th>
                        <th>Importe</th>
                        <th>Fecha</th>
                        <th>Tipo de pago</th>
                        <!-- <th>Método de pago</th> -->
                        <th>Estado</th>
                        <th>Próxima fecha de cobro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas as $factura)
                        <tr>
                            <td>{{ $factura->id }}</td>
                            <td>{{ $factura->pago->nombre ?? 'N/A' }}</td>
                            <td>{{ $factura->pago->email ?? '-' }}</td>
                            <td>
                                @if($factura->user && method_exists($factura->user, 'hasRole'))
                                    @if($factura->user->hasRole('Administrador'))
                                        Administrador
                                    @elseif($factura->user->hasRole('Profesor'))
                                        Profesor
                                    @elseif($factura->user->hasRole('Alumno'))
                                        Alumno
                                    @elseif($factura->user->hasRole('Editor'))
                                        Editor
                                    @else
                                        -
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $factura->producto }}</td>
                            <td>€{{ number_format($factura->importe, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $factura->pago->tipo_pago === 'mensual' ? 'Mensual' : 'Único' }}</td>
                            <!-- <td>{{ $factura->pago->metodo_pago ?? '-' }}</td> -->
                            <td>
                                <span class="badge bg-success">Pagado</span>
                            </td>
                            <td>
                                @if($factura->pago->tipo_pago === 'mensual' && ($factura->pago->pendiente ?? 0) > 0)
                                    @php
                                        $fecha_ultimo_pago = $factura->pago->fecha_ultimo_pago ?? $factura->fecha;
                                        $intervalo = $factura->pago->intervalo_mensual ?? 1;
                                        $proxima_fecha = \Carbon\Carbon::parse($fecha_ultimo_pago)->addMonths($intervalo);
                                    @endphp
                                    <span class="badge bg-info" style="background:#6366f1;color:#fff;">{{ $proxima_fecha->format('d/m/Y') }}</span>
                                    <div style="font-size:0.95em;color:#64748b;">Próxima cuota</div>
                                @else
                                    <span style="color:#22c55e;font-weight:600;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="facturas-empty">No hay facturas registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection