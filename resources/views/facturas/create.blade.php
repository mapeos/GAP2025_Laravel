@extends('template.base-admin')

@section('title', 'Nueva Factura')
@section('title-sidebar', 'Facturas')
@section('title-page', 'Generar Factura')

@section('content')
    <h1>Generar Factura</h1>
    <form action="{{ route('facturas.store') }}" method="POST">
        @csrf
        <input type="hidden" name="pago_id" value="{{ request('pago_id') ?? ($pago->id_pago ?? '') }}">
        <div style="margin-bottom: 1rem;">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $pago->nombre ?? '') }}" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email', $pago->email ?? '') }}" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="producto">Producto/Curso:</label>
            <input type="text" name="producto" id="producto" value="{{ old('producto', $pago->curso ?? '') }}" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="importe">Importe:</label>
            <input type="number" step="0.01" name="importe" id="importe" value="{{ old('importe', $pago->importe ?? '') }}" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" value="{{ old('fecha', isset($pago) && $pago ? ($pago->fecha ? \Carbon\Carbon::parse($pago->fecha)->toDateString() : now()->toDateString()) : now()->toDateString()) }}" required>
        </div>
        <div style="margin-bottom: 1rem;">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado">
                <option value="pagada">Pagada</option>
                <option value="pendiente">Pendiente</option>
            </select>
        </div>
        <button type="submit" style="background: #4CAF50; color: white; padding: 8px 16px; border: none; border-radius: 4px;">Guardar Factura</button>
    </form>
@endsection
