@php
use Carbon\Carbon;
@endphp

<div class="card card-hover mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="ri-bank-card-line me-2"></i>Mis pagos y próximas cuotas</h5>
    </div>
    <div class="card-body">
        @if($user->facturas && $user->facturas->count())
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Curso/Servicio</th>
                        <th>Importe</th>
                        <th>Tipo de pago</th>
                        <th>Estado</th>
                        <th>Próxima fecha de cobro</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->facturas as $factura)
                        <tr>
                            <td>{{ $factura->producto }}</td>
                            <td>€{{ number_format($factura->importe,2) }}</td>
                            <td>{{ $factura->pago->tipo_pago === 'mensual' ? 'Mensual' : 'Único' }}</td>
                            <td>
                                <span class="badge bg-success">Pagado</span>
                            </td>
                            <td>
                                @if($factura->pago->tipo_pago === 'mensual' && ($factura->pago->pendiente ?? 0) > 0)
                                    @php
                                        $fecha_ultimo_pago = $factura->pago->fecha_ultimo_pago ?? $factura->fecha;
                                        $intervalo = $factura->pago->intervalo_mensual ?? 1;
                                        $proxima_fecha = Carbon::parse($fecha_ultimo_pago)->addMonths($intervalo);
                                    @endphp
                                    <span class="badge bg-info" style="background:#6366f1;color:#fff;">{{ $proxima_fecha->format('d/m/Y') }}</span>
                                @else
                                    <span style="color:#22c55e;font-weight:600;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">No tienes pagos registrados.</div>
        @endif
    </div>
</div>
