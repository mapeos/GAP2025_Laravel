{{-- resources/views/landing/partials/newsletter.blade.php --}}
{{-- Contenido migrado de newsletter.htm --}}
{{-- Copia aquí el contenido de newsletter.htm y ajusta las rutas de los assets a /assets/landing/ --}}

<!-- newsletter -->
<section class="newsletter">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h2>¿Aún no te has registrado?</h2>
        <p class="mb-5">No te pierdas las novedades, cursos y oportunidades de Auria Academy.<br>¡Regístrate ahora y forma parte de nuestra comunidad!</p>
      </div>
      <div class="col-lg-8 col-sm-10 col-12 mx-auto text-center">
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg" style="min-width:200px; font-weight:600;">Registrarse</a>
      </div>
    </div>
  </div>
  <!-- background shapes -->
  <img class="newsletter-bg-shape left-right-animation" src="/assets/landing/images/background-shape/seo-ball-2.png" alt="background-shape">
</section>
<!-- /newsletter -->
