{{-- resources/views/landing/partials/pricing.blade.php --}}
{{-- Contenido migrado de pricing.htm --}}
{{-- Copia aquí el contenido de pricing.htm y ajusta las rutas de los assets a /assets/landing/ --}}

<!-- pricing -->
<section class="section-lg pb-0 pricing" id="pricing">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h2 class="section-title">Planes Auria Academy</h2>
        <p class="mb-50">Elige el plan que mejor se adapte a tu camino de aprendizaje.<br>
          Acceso flexible, precios claros y sin sorpresas.</p>
      </div>
      <div class="col-lg-10 mx-auto">
        <div class="row justify-content-center">
          <!-- pricing table: Gratis -->
          <div class="col-md-6 col-lg-4 mb-5 mb-lg-0">
            <div class="rounded text-center pricing-table table-1">
              <h3>Plan Gratuito</h3>
              <h1><span>€</span>0</h1>
              <p>Acceso a cursos introductorios, recursos gratuitos y participación en la comunidad básica.</p>
              <a href="{{ route('register') }}" class="btn pricing-btn px-2">Comenzar gratis</a>
            </div>
          </div>
          <!-- pricing table: Estudiante -->
          <div class="col-md-6 col-lg-4 mb-5 mb-lg-0">
            <div class="rounded text-center pricing-table table-2">
              <h3>Plan Estudiante</h3>
              <h1><span>€</span>19</h1>
              <p>Acceso ilimitado a todos los cursos, talleres en vivo, certificados digitales y soporte prioritario.</p>
              <a href="{{ route('register') }}" class="btn pricing-btn px-2">Elegir Estudiante</a>
            </div>
          </div>
          <!-- pricing table: Pro -->
          <div class="col-md-6 col-lg-4 mb-5 mb-lg-0">
            <div class="rounded text-center pricing-table table-3">
              <h3>Plan Pro</h3>
              <h1><span>€</span>39</h1>
              <p>Incluye todo lo del plan Estudiante + mentorías personalizadas, acceso a eventos exclusivos y material premium.</p>
              <a href="{{ route('register') }}" class="btn pricing-btn px-2">Elegir Pro</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- background shapes -->
  <img class="pricing-bg-shape-1 up-down-animation" src="/assets/landing/images/background-shape/seo-ball-1.png" alt="background-shape">
  <img class="pricing-bg-shape-2 up-down-animation" src="/assets/landing/images/background-shape/seo-half-cycle.png" alt="background-shape">
  <img class="pricing-bg-shape-3 left-right-animation" src="/assets/landing/images/background-shape/team-bg-triangle.png" alt="background-shape">
</section>
<!-- /pricing -->
