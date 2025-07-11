@extends('template.base-alumno')
@section('title', 'Mis Facturas')
@section('title-sidebar', auth()->user()?->name ?? 'Alumno')
@section('title-page', 'Mis Facturas')

@section('content')
<style>
    .facturas-card {
        max-width: 900px;
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
    .badge.bg-info {
        background: #6366f1;
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
    <h5 class="card-title"><i class="ri-file-list-3-line me-2"></i>Mis Facturas</h5>
    <div class="table-responsive">
        <table class="facturas-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Curso</th>
                    <th>Importe</th>
                    <th>Fecha</th>
                    <th>Tipo de pago</th>
                    <th>Estado</th>
                    <th>Próxima cuota</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facturas as $factura)
                    <tr>
                        <td>{{ $factura->id }}</td>
                        <td>{{ $factura->producto }}</td>
                        <td>€{{ number_format($factura->importe, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $factura->pago->tipo_pago === 'mensual' ? 'Mensual' : 'Único' }}</td>
                        <td><span class="badge bg-success">Pagado</span></td>
                        <td>
                            @if($factura->pago->tipo_pago === 'mensual' && ($factura->pago->pendiente ?? 0) > 0)
                                @php
                                    $fecha_ultimo_pago = $factura->pago->fecha_ultimo_pago ?? $factura->fecha;
                                    $intervalo = $factura->pago->intervalo_mensual ?? 1;
                                    $proxima_fecha = \Carbon\Carbon::parse($fecha_ultimo_pago)->addMonths($intervalo);
                                @endphp
                                <span class="badge bg-info">{{ $proxima_fecha->format('d/m/Y') }}</span>
                            @else
                                <span style="color:#22c55e;font-weight:600;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="facturas-empty">No tienes facturas registradas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
