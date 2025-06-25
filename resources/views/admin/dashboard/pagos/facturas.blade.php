@extends('template.base')
@section('title', 'Gestión de Facturas')
@section('title-sidebar', auth()->user()?->name ?? 'Dashboard Admin')
@section('title-page', 'Gestión de Facturas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Facturas</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Gestión de Facturas</h5>
                    <a href="{{ route('admin.pagos.facturas.create') }}" class="btn btn-primary">
                        <i class="ri-add-line"></i> Nueva Factura
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Producto</th>
                                    <th>Importe</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $facturas = \App\Models\Factura::with('user')->orderBy('fecha', 'desc')->get();
                                @endphp
                                @forelse($facturas as $factura)
                                    <tr>
                                        <td>{{ $factura->id }}</td>
                                        <td>{{ $factura->user->name ?? 'N/A' }}</td>
                                        <td>{{ $factura->producto }}</td>
                                        <td>€{{ number_format($factura->importe, 2) }}</td>
                                        <td>{{ $factura->fecha->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $factura->estado === 'pagado' ? 'success' : 'warning' }}">
                                                {{ ucfirst($factura->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay facturas registradas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 