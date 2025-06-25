{{-- resources/views/landing/partials/navigation.blade.php --}}
{{-- Contenido migrado de navigation.htm --}}
{{-- Copia aquí el contenido de navigation.htm y ajusta las rutas de los assets a /assets/landing/ --}}

<!-- navigation -->
<section class="fixed-top navigation">
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light justify-content-center">
      <a class="navbar-brand d-flex align-items-center mx-auto" href="/" style="font-weight: bold; font-size: 1.1rem; color: #6366f1; letter-spacing: -1px; margin-right: 4rem !important;">
        Auria Academy
      </a>
      <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- navbar -->
      <div class="collapse navbar-collapse justify-content-center text-center" id="navbar">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <a class="nav-link" href="/">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link page-scroll" href="#feature">Características</a>
          </li>
          <li class="nav-item">
            <a class="nav-link page-scroll" href="#team">Equipo</a>
          </li>
          <li class="nav-item">
            <a class="nav-link page-scroll" href="#pricing">Planes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/contacto">Contacto</a>
          </li>
        </ul>
        <div class="d-flex align-items-center justify-content-center flex-nowrap w-100" style="gap:0.4rem; white-space:nowrap; justify-content: center !important;">
          <a href="{{ route('login') }}" class="btn btn-primary btn-sm primary-shadow" style="min-width:80px; font-size:0.95rem; padding:0.3rem 0.7rem;">Entrar</a>
          <a href="{{ route('password.request') }}" class="btn btn-primary btn-sm primary-shadow" style="min-width:120px; font-size:0.90rem; padding:0.3rem 0.7rem;">¿Olvidaste?</a>
        </div>
      </div>
    </nav>
  </div>
</section>
<!-- /navigation -->
