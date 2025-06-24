{{-- resources/views/landing/partials/hero-area.blade.php --}}
{{-- Contenido migrado de hero-area.htm --}}
{{-- Copia aquí el contenido de hero-area.htm y ajusta las rutas de los assets a /assets/landing/ --}}

<!-- hero area -->
<section class="hero-section hero" data-background="" style="background-image: url('/assets/landing/images/hero-area/banner-bg.png');">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center zindex-1">
        <h1 class="mb-3" style="font-weight:700; color:#222;">Bienvenido a Auria Academy</h1>
        <p class="mb-4" style="font-size:1.25rem; color:#555;">
          La plataforma donde tu aprendizaje y crecimiento profesional son nuestra prioridad.<br>
          Descubre cursos, talleres y recursos exclusivos para potenciar tu futuro.
        </p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="min-width:180px; font-weight:600;">Registrarse</a>
        <!-- banner image -->
        <img class="img-fluid w-100 banner-image mt-4" src="/assets/landing/images/hero-area/banner-img.png" alt="banner-img">
      </div>
    </div>
  </div>
  <!-- background shapes -->
  <div id="scene">
    <img class="img-fluid hero-bg-1 up-down-animation" src="/assets/landing/images/background-shape/feature-bg-2.png" alt="">
    <img class="img-fluid hero-bg-2 left-right-animation" src="/assets/landing/images/background-shape/seo-ball-1.png" alt="">
    <img class="img-fluid hero-bg-3 left-right-animation" src="/assets/landing/images/background-shape/seo-half-cycle.png" alt="">
    <img class="img-fluid hero-bg-4 up-down-animation" src="/assets/landing/images/background-shape/green-dot.png" alt="">
    <img class="img-fluid hero-bg-5 left-right-animation" src="/assets/landing/images/background-shape/blue-half-cycle.png" alt="">
    <img class="img-fluid hero-bg-6 up-down-animation" src="/assets/landing/images/background-shape/seo-ball-1.png" alt="">
    <img class="img-fluid hero-bg-7 left-right-animation" src="/assets/landing/images/background-shape/yellow-triangle.png" alt="">
    <img class="img-fluid hero-bg-8 up-down-animation" src="/assets/landing/images/background-shape/service-half-cycle.png" alt="">
    <img class="img-fluid hero-bg-9 up-down-animation" src="/assets/landing/images/background-shape/team-bg-triangle.png" alt="">
  </div>
</section>
<!-- /hero-area -->
