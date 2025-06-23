@extends('template.base-admin')

@section('title', 'Facturas')
@section('title-sidebar', 'Facturas')
@section('title-page', 'Mis Facturas')

@section('content')
    <h1>Mis Facturas</h1>
    <form method="GET" action="" style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
        <div>
            <label for="buscar">Buscar:</label><br>
            <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" placeholder="Producto o estado">
        </div>
        <div>
            <label for="fecha_inicio">Desde:</label><br>
            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}">
        </div>
        <div>
            <label for="fecha_fin">Hasta:</label><br>
            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}">
        </div>
        <div>
            <button type="submit" style="background: #4CAF50; color: white; padding: 8px 16px; border: none; border-radius: 4px;">Filtrar</button>
        </div>
    </form>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f2f2f2;">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($facturas as $factura)
                <tr>
                    <td>{{ $factura->id }}</td>
                    <td>{{ $factura->producto }}</td>
                    <td>{{ $factura->importe }} €</td>
                    <td>{{ $factura->fecha }}</td>
                    <td>{{ $factura->estado }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No tienes facturas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
