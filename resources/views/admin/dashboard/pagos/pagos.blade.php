@extends('template.base')
@section('title', 'Dashboard')
@section('title-sidebar', 'Dashboard Admin')
@section('title-page', 'Gestión de pagos')

@section('breadcrumb')
    <li class="breadcrumb-item active"> Test </li> 
@endsection 


@section('content')
<div class="container my-5">

  <!-- Lista de cursos -->
  <div class="card shadow-sm p-4 mb-4">
    <h1 class="card-title h4 mb-3">CURSOS DISPONIBLES:</h1>
    <p class="mb-4">A continuación se muestran los cursos que puedes adquirir:</p>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-light">
          <tr>
            <th>Título del curso</th>
            <th>Resumen</th>
            <th>Fecha de inicio</th>
            <th>Fecha de finalización</th>
            <th>Seleccionar</th>
          </tr>
        </thead>
        <tbody>
          <tr onclick="seleccionarCurso('Auxiliar de Enfermería', 'Formación completa para desempeñar funciones básicas de asistencia sanitaria en centros de salud y hospitales.', '3 de junio de 2025', '30 de septiembre de 2025', 180)">
            <td>Auxiliar de Enfermería</td>
            <td>Formación completa para desempeñar funciones básicas de asistencia sanitaria en centros de salud y hospitales.</td>
            <td>3 de junio de 2025</td>
            <td>30 de septiembre de 2025</td>
            <td><button class="btn btn-sm btn-primary">Seleccionar</button></td>
          </tr>
          <tr onclick="seleccionarCurso('Administración y Gestión', 'Curso enfocado en técnicas administrativas, contabilidad básica y gestión documental en entornos de oficina.', '10 de junio de 2025', '10 de octubre de 2025', 200)">
            <td>Administración y Gestión</td>
            <td>Curso enfocado en técnicas administrativas, contabilidad básica y gestión documental en entornos de oficina.</td>
            <td>10 de junio de 2025</td>
            <td>10 de octubre de 2025</td>
            <td><button class="btn btn-sm btn-primary">Seleccionar</button></td>
          </tr>
          <tr onclick="seleccionarCurso('Informática Básica', 'Aprende a utilizar ordenadores, navegar por internet, trabajar con documentos y manejar programas de ofimática.', '17 de junio de 2025', '17 de agosto de 2025', 150)">
            <td>Informática Básica</td>
            <td>Aprende a utilizar ordenadores, navegar por internet, trabajar con documentos y manejar programas de ofimática.</td>
            <td>17 de junio de 2025</td>
            <td>17 de agosto de 2025</td>
            <td><button class="btn btn-sm btn-primary">Seleccionar</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Resumen del curso y forma de pago -->
  <div class="card shadow-sm p-4 mb-4">
    <h1 class="card-title h4 mb-3">RESUMEN DEL CURSO:</h1>
    <p class="mb-4">En esta sección, encontrarás un resumen del curso seleccionado.</p>

    <div class="mb-3">
      <p><strong>Curso:</strong> <span id="curso_titulo">—</span></p>
      <p><strong>Precio:</strong> <span id="curso_precio">—</span></p>
      <p><strong>Fecha de inicio:</strong> <span id="curso_inicio">—</span></p>
      <p><strong>Fecha de finalización:</strong> <span id="curso_fin">—</span></p>
    </div>

    <h5 class="mt-4">Tipo de pago</h5>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="tipo_pago" value="unico" id="pagoUnico" checked onchange="actualizarTipoPago()" />
      <label class="form-check-label" for="pagoUnico">Pago único</label>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="radio" name="tipo_pago" value="mensual" id="pagoMensual" onchange="actualizarTipoPago()" />
      <label class="form-check-label" for="pagoMensual">Pago mensual</label>
    </div>

    <div id="campo_meses" class="form-group" style="display:none;">
      <label for="meses">Número de meses:</label>
      <input type="number" class="form-control w-25" id="meses" min="1" value="1" onchange="calcularMensualidad()" />
    </div>

    <p id="detalle_pago" class="fw-bold text-primary mt-3"></p>

    <h5 class="mt-4">Métodos de pago</h5>
    <form id="form_pago" onsubmit="event.preventDefault(); procesarPago();">
      <div class="form-group">
        <label for="metodo_pago">Selecciona un método:</label>
        <select id="metodo_pago" class="form-control w-50" onchange="mostrarFormulario()">
          <option value="">-- Elige una opción --</option>
          <option value="tarjeta">Tarjeta de Crédito</option>
          <option value="paypal">PayPal</option>
          <option value="efectivo">Pago en efectivo</option>
          <option value="transferencia">Transferencia bancaria</option>
        </select>
      </div>

      <!-- Tarjeta -->
      <div id="form_tarjeta" style="display: none;" class="mt-3">
        <h5>Pago con Tarjeta</h5>
        <input type="text" class="form-control mb-2" placeholder="Número de tarjeta" />
        <input type="text" class="form-control mb-2" placeholder="MM/AA" />
        <input type="text" class="form-control mb-2" placeholder="CVV" />
      </div>

      <!-- PayPal -->
      <div id="form_paypal" style="display: none;" class="mt-3">
        <h5>Pago con PayPal</h5>
        <p>Serás redirigido a PayPal </p>
        <button type="button" class="btn btn-secondary" disabled>Ir a PayPal</button>
      </div>

      <!-- Efectivo -->
      <div id="form_efectivo" style="display: none;" class="mt-3">
        <h5>Pago en efectivo</h5>
        <p>Podrás abonar el importe directamente en la Academia.</p>
        <p>Cuando el pago se haya realizado, podrás acceder al curso.</p>
      </div>

      <!-- Transferencia -->
      <div id="form_transferencia" style="display: none;" class="mt-3">
        <h5>Pago por Transferencia Bancaria</h5>
        <p>Por favor realiza la transferencia a los siguientes datos:</p>
        <ul>
          <li><strong>Titular de la cuenta:</strong> Academia </li>
          <li><strong>IBAN:</strong> ES12 3456 7890 1234 5678 9012</li>
          <li><strong>Importe:</strong> <span id="curso_precio_transferencia">—</span></li>
          <li><strong>Concepto:</strong> Nombre usuario + <span id="curso_titulo_transferencia">—</span></li>
        </ul>
        <p>Gracias por realizar la transferencia. Una vez confirmada la recepción del pago, podrás comenzar el curso.</p>
        <p>Si no has adjuntado el justificante, por favor envíalo a: pagos@tuacademia.com</p>
      </div>

      <button type="submit" class="btn btn-primary mt-4">Confirmar pago</button>

      <div id="mensaje_pago" style="margin-top: 20px; display: none;"></div>
    </form>
  </div>

  <div class="mb-4 d-flex gap-2">
    <a href="{{ route('admin.pagos.metodos') }}" class="btn btn-outline-primary">Métodos</a>
    <a href="{{ route('admin.pagos.facturas.index') }}" class="btn btn-outline-primary">Facturas</a>
    <a href="{{ route('admin.pagos.servicios') }}" class="btn btn-primary">Servicios</a>
  </div>
</div>

<script>
  let precioCursoSeleccionado = 0;

  function seleccionarCurso(titulo, resumen, inicio, fin, precio) {
    precioCursoSeleccionado = precio;
    document.getElementById('curso_titulo').innerText = titulo;
    document.getElementById('curso_precio').innerText = precio + ' €';
    document.getElementById('curso_inicio').innerText = inicio;
    document.getElementById('curso_fin').innerText = fin;
    document.getElementById('curso_precio_transferencia').innerText = precio + ' €';
    document.getElementById('curso_titulo_transferencia').innerText = titulo;
    document.getElementById('mensaje_pago').style.display = 'none';
    document.getElementById('meses').value = 1;
    actualizarTipoPago();
  }

  function actualizarTipoPago() {
    const tipo = document.querySelector('input[name="tipo_pago"]:checked').value;
    const campoMeses = document.getElementById('campo_meses');
    const detallePago = document.getElementById('detalle_pago');

    if (tipo === 'mensual') {
      campoMeses.style.display = 'block';
      calcularMensualidad();
    } else {
      campoMeses.style.display = 'none';
      detallePago.innerText = `Precio final: ${precioCursoSeleccionado.toFixed(2)} €`;
      document.getElementById('curso_precio_transferencia').innerText = `${precioCursoSeleccionado.toFixed(2)} €`;
    }
  }

  function calcularMensualidad() {
    const meses = parseInt(document.getElementById('meses').value);
    const detallePago = document.getElementById('detalle_pago');
    const precioTransferencia = document.getElementById('curso_precio_transferencia');

    if (isNaN(meses) || meses <= 0) {
      detallePago.innerText = 'Introduce un número válido de meses.';
      precioTransferencia.innerText = '—';
      return;
    }

    const mensualidad = precioCursoSeleccionado / meses;
    detallePago.innerText = `Pagarás ${mensualidad.toFixed(2)} € cada mes durante ${meses} meses.`;

    const tipoPago = document.querySelector('input[name="tipo_pago"]:checked').value;
    if (tipoPago === 'mensual') {
      precioTransferencia.innerText = `${mensualidad.toFixed(2)} € al mes durante ${meses} meses`;
    } else {
      precioTransferencia.innerText = `${precioCursoSeleccionado.toFixed(2)} €`;
    }
  }

  function mostrarFormulario() {
    const metodo = document.getElementById('metodo_pago').value;
    ['tarjeta', 'efectivo', 'paypal', 'transferencia'].forEach(id => {
      document.getElementById('form_' + id).style.display = 'none';
    });
    if (metodo) {
      document.getElementById('form_' + metodo).style.display = 'block';
    }
  }

  function procesarPago() {
    const metodo = document.getElementById('metodo_pago').value;
    const mensajeDiv = document.getElementById('mensaje_pago');
    mensajeDiv.style.display = 'block';

    if (!metodo) {
      alert('Por favor selecciona un método de pago');
      mensajeDiv.style.display = 'none';
      return;
    }

    if (metodo === 'transferencia') {
      mensajeDiv.innerHTML = "<p class='text-success'>Gracias por realizar la transferencia. Una vez confirmada la recepción del pago, podrás comenzar el curso.</p>";
    } else {
      const exito = Math.random() < 0.8;
      mensajeDiv.innerHTML = exito
        ? "<p class='text-success'>Pago exitoso. Ahora puedes acceder al curso.</p>"
        : "<p class='text-danger'>Pago denegado. Intenta de nuevo o elige otro método.</p>";
    }
  }

  window.onload = function () {
    mostrarFormulario();
    document.querySelectorAll('input[name="tipo_pago"]').forEach(radio => {
      radio.addEventListener('change', actualizarTipoPago);
    });
  };
</script>
@endsection
