@extends('template.base-admin')

@section('title', 'Métodos de Pago')
@section('title-sidebar', 'Gestión de Pagos')
@section('title-page', 'Selecciona un método de pago')

@section('content')
    <style>
        .pago-form {
            max-width: 480px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px 0 #0001;
            padding: 2rem 2.5rem 2.5rem 2.5rem;
        }
        .pago-form h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #2d3748;
        }
        .pago-form label {
            font-weight: 500;
            color: #444;
        }
        .pago-form input[type="text"],
        .pago-form input[type="email"],
        .pago-form input[type="number"] {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-top: 0.25rem;
            margin-bottom: 1.2rem;
            font-size: 1rem;
            background: #f8fafc;
            transition: border 0.2s;
        }
        .pago-form input:focus {
            border: 1.5px solid #4f46e5;
            outline: none;
            background: #fff;
        }
        .pago-form fieldset {
            border: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        .pago-form legend {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6366f1;
            margin-bottom: 0.5rem;
        }
        .pago-form .radio-group {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            margin-bottom: 1.5rem;
        }
        .pago-form .radio-group label {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-weight: 400;
            background: #f3f4f6;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            transition: border 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .pago-form .radio-group label:hover, .pago-form .radio-group input[type="radio"]:focus + span {
            border: 1.5px solid #6366f1;
            box-shadow: 0 2px 8px 0 #6366f122;
        }
        .pago-form .radio-group input[type="radio"] {
            accent-color: #6366f1;
            margin-right: 0.5rem;
        }
        .pago-form .metodo-icono {
            min-width: 32px;
            max-width: 48px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            vertical-align: middle;
            margin-left: 0.5rem;
            margin-right: 0;
            font-size: 1.25em;
        }
        .pago-form .metodo-icono img[alt="Visa"],
        .pago-form .metodo-icono img[alt="Mastercard"] {
            height: 20px !important;
            max-width: 32px;
            margin-left: 0;
            margin-right: 2px;
        }
        .pago-form .metodo-icono img[alt="PayPal"] {
            height: 32px !important;
            max-width: 56px;
        }
        .pago-form button[type="submit"] {
            background: linear-gradient(90deg, #6366f1 60%, #4f46e5 100%);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 0.7rem 2.2rem;
            font-size: 1.1rem;
            margin-top: 1.5rem;
            cursor: pointer;
            box-shadow: 0 2px 8px 0 #6366f133;
            transition: background 0.2s;
        }
        .pago-form button[type="submit"]:hover {
            background: linear-gradient(90deg, #4f46e5 60%, #6366f1 100%);
        }
        .pago-form .info {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 1.2rem;
        }
        .pago-form .success, .pago-form .error {
            padding: 0.7rem 1rem;
            border-radius: 6px;
            margin-bottom: 1.2rem;
            font-size: 1rem;
        }
        .pago-form .success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        .pago-form .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        .pago-form #formulario-dinamico > div {
            margin-bottom: 1.2rem;
        }
        .pago-form .efectivo-msg {
            background: #f3f4f6;
            color: #374151;
            border-radius: 6px;
            padding: 0.7rem 1rem;
            margin-top: 0.5rem;
            margin-bottom: 1.2rem;
            font-size: 1rem;
            border: 1px solid #e5e7eb;
            max-width: 100%;
            box-sizing: border-box;
        }
    </style>
    <div class="pago-form">
        <h1>Método de pago</h1>
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="error">
                <ul style="margin: 0; padding-left: 1.2em;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.pagos.metodos.store') }}" method="POST" id="form-metodo-pago">
            @csrf
            <fieldset>
                <legend>Datos del usuario y curso</legend>
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
                <label for="curso">Tipo de curso:</label>
                <input type="text" name="curso" id="curso" required placeholder="Ej: Curso de inglés, Excel, etc.">
            </fieldset>
            <div class="radio-group">
                <label><input type="radio" name="tipo_pago" value="unico" checked> Pago único</label>
                <label><input type="radio" name="tipo_pago" value="mensual"> Pago mensual</label>
            </div>
            <div id="campo-meses" style="display: none;">
                <label for="meses">Número de meses:</label>
                <input type="number" name="meses" id="meses" min="1" value="1" style="width: 80px; display: inline-block;">
                <span id="precio-mensual" class="info"></span>
            </div>
            <div class="radio-group">
                <label><input type="radio" name="metodo_pago" value="tarjeta" required> <span>Tarjeta de crédito/débito</span>
                    <span class="metodo-icono" style="margin-left:auto;">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa"> 
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="Mastercard">
                    </span>
                </label>
                <label><input type="radio" name="metodo_pago" value="transferencia"> <span>Transferencia bancaria</span>
                    <span class="metodo-icono" style="margin-left:auto;">🏦</span>
                </label>
                <label><input type="radio" name="metodo_pago" value="paypal"> <span>PayPal</span>
                    <span class="metodo-icono" style="margin-left:auto;">
                        <img src='https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg' alt='PayPal'>
                    </span>
                </label>
                <label><input type="radio" name="metodo_pago" value="efectivo"> <span>Efectivo</span>
                    <span class="metodo-icono" style="margin-left:auto;">💶</span>
                </label>
            </div>
            <div id="formulario-dinamico"></div>
            <button type="submit">Pagar</button>
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
                        html = `<div><label>Número de tarjeta:<br><input type='text' name='numero_tarjeta' id='numero_tarjeta' maxlength='19' pattern='(?:\\d[ ]?){16,19}' required autocomplete='cc-number' inputmode='numeric'></label></div>
                                <div><label>Fecha de caducidad:<br><input type='text' name='caducidad' id='caducidad' maxlength='5' placeholder='MM/AA' required autocomplete='cc-exp'></label></div>
                                <div><label>CVV:<br><input type='text' name='cvv' id='cvv' maxlength='4' pattern='\\d{3,4}' required autocomplete='cc-csc' inputmode='numeric'></label></div>`;
                    } else if (this.value === 'transferencia') {
                        html = `<div><label>IBAN:<br><input type='text' name='iban' id='iban' maxlength='29' pattern='ES[0-9]{2}(?: [0-9A-Z]{4}){5,6}' placeholder='ES61 1234 3456 42 0456 3235 32' required autocomplete='off'></label></div>`;
                    } else if (this.value === 'paypal') {
                        html = `<div class='efectivo-msg'><em>Al continuar, serás redirigido a PayPal para completar el pago de forma segura.</em></div>`;
                    } else if (this.value === 'efectivo') {
                        html = `<div class='efectivo-msg'><em>El pago en efectivo se realiza presencialmente.</em></div>`;
                    }
                    formDinamico.innerHTML = html;

                    // Validaciones y formateos para tarjeta
                    if (this.value === 'tarjeta') {
                        const numTarjeta = document.getElementById('numero_tarjeta');
                        const caducidad = document.getElementById('caducidad');
                        const cvv = document.getElementById('cvv');
                        // Solo números y máximo 16-19 dígitos
                        numTarjeta.addEventListener('input', function(e) {
                            let val = this.value.replace(/\D/g, '');
                            // Formato 0000 0000 0000 0000
                            val = val.replace(/(.{4})/g, '$1 ').trim();
                            this.value = val.substring(0, 19);
                        });
                        // Formato MM/AA automático
                        caducidad.addEventListener('input', function(e) {
                            let val = this.value.replace(/\D/g, '');
                            if (val.length > 2) val = val.substring(0,2) + '/' + val.substring(2,4);
                            this.value = val.substring(0,5);
                        });
                        // Solo números y máximo 4 dígitos para CVV
                        cvv.addEventListener('input', function(e) {
                            this.value = this.value.replace(/\D/g, '').substring(0,4);
                        });
                    }
                    // Validación IBAN: solo letras y números, mayúsculas
                    if (this.value === 'transferencia') {
                        const iban = document.getElementById('iban');
                        iban.setAttribute('maxlength', '29'); // 24 caracteres + 5 espacios
                        iban.setAttribute('pattern', 'ES[0-9]{2}(?: [0-9A-Z]{4}){5,6}');
                        iban.setAttribute('placeholder', 'ES61 1234 3456 42 0456 3235 32');
                        iban.addEventListener('input', function(e) {
                            let val = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                            // Forzar que empiece por ES
                            if (!val.startsWith('ES')) {
                                val = 'ES' + val.replace(/^ES+/,'');
                            }
                            val = val.substring(0,24);
                            // Agrupar en bloques de 4
                            let bloques = val.match(/.{1,4}/g);
                            this.value = bloques ? bloques.join(' ') : '';
                        });
                    }
                });
            });
        });
    </script>
@endsection
