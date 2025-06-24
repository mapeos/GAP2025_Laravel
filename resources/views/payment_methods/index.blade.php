@extends('template.base-admin')

@section('title', 'Métodos de Pago')
@section('title-sidebar', 'Gestión de Pagos')
@section('title-page', 'Selecciona un método de pago')

@section('content')
    <div style="margin-bottom: 1rem;">
        <h1>Selecciona un método de pago</h1>
        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 1rem; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 1rem; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 1.2em;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('pagos.store') }}" method="POST" id="form-metodo-pago">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label><strong>Tipo de pago:</strong></label>
                <label style="margin-left: 1rem;">
                    <input type="radio" name="tipo_pago" value="unico" checked> Pago único
                </label>
                <label style="margin-left: 1rem;">
                    <input type="radio" name="tipo_pago" value="mensual"> Pago mensual
                </label>
            </div>
            <div id="campo-meses" style="display: none; margin-bottom: 1.5rem;">
                <label for="meses">Número de meses:</label>
                <input type="number" name="meses" id="meses" min="1" value="1" style="width: 60px;">
                <span id="precio-mensual" style="margin-left: 1rem;"></span>
            </div>
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="radio" name="metodo_pago" value="tarjeta" required>
                    <span>Tarjeta de crédito/débito</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="radio" name="metodo_pago" value="transferencia">
                    <span>Transferencia bancaria</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="radio" name="metodo_pago" value="paypal">
                    <span>PayPal</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="radio" name="metodo_pago" value="efectivo">
                    <span>Efectivo</span>
                </label>
            </div>
            <div id="formulario-dinamico" style="margin-top: 2rem;"></div>
            <button type="submit" style="margin-top: 2rem; background: #4CAF50; color: white; padding: 8px 16px; border: none; border-radius: 4px;">Pagar</button>
        </form>
    </div>
    <script>
        // Simulación de precio total (puedes cambiarlo por el precio real del curso)
        const precioTotal = 120;
        document.addEventListener('DOMContentLoaded', function() {
            const radiosTipo = document.querySelectorAll('input[name="tipo_pago"]');
            const campoMeses = document.getElementById('campo-meses');
            const inputMeses = document.getElementById('meses');
            const precioMensual = document.getElementById('precio-mensual');
            function actualizarMensualidad() {
                const meses = parseInt(inputMeses.value) || 1;
                const mensualidad = (precioTotal / meses).toFixed(2);
                precioMensual.textContent = `(${mensualidad} € al mes)`;
            }
            radiosTipo.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'mensual') {
                        campoMeses.style.display = 'block';
                        actualizarMensualidad();
                    } else {
                        campoMeses.style.display = 'none';
                    }
                });
            });
            inputMeses.addEventListener('input', actualizarMensualidad);
            // Mostrar campos según el método seleccionado
            const radiosMetodo = document.querySelectorAll('input[name="metodo_pago"]');
            const formDinamico = document.getElementById('formulario-dinamico');
            radiosMetodo.forEach(radio => {
                radio.addEventListener('change', function() {
                    let html = '';
                    if (this.value === 'tarjeta') {
                        html = `<div><label>Número de tarjeta:<br><input type='text' name='numero_tarjeta' required></label></div>
                                <div><label>Fecha de caducidad:<br><input type='text' name='caducidad' placeholder='MM/AA' required></label></div>
                                <div><label>CVV:<br><input type='text' name='cvv' required></label></div>`;
                    } else if (this.value === 'transferencia') {
                        html = `<div><label>IBAN:<br><input type='text' name='iban' required></label></div>`;
                    } else if (this.value === 'paypal') {
                        html = `<div><label>Email de PayPal:<br><input type='email' name='paypal_email' required></label></div>`;
                    } else if (this.value === 'efectivo') {
                        html = `<div><em>El pago en efectivo se realiza presencialmente.</em></div>`;
                    }
                    formDinamico.innerHTML = html;
                });
            });
        });
    </script>
@endsection
