{{-- resources/views/landing/partials/navigation.blade.php --}}
{{-- Contenido migrado de navigation.htm --}}
{{-- Copia aquí el contenido de navigation.htm y ajusta las rutas de los assets a /assets/landing/ --}}

<!-- navigation -->
<section class="fixed-top navigation">
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light">
      <a class="navbar-brand d-flex align-items-center" href="/" style="font-weight: bold; font-size: 1.5rem; color: #6366f1;">
        Auria Academy
      </a>
      <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- navbar -->
      <div class="collapse navbar-collapse text-center" id="navbar">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="/">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link page-scroll" href="#feature">Feature</a>
          </li>
          <li class="nav-item">
            <a class="nav-link page-scroll" href="#team">Team</a>
          </li>
          <li class="nav-item">
            <a class="nav-link page-scroll" href="#pricing">Pricing</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/contact">Contact</a>
          </li>
        </ul>
        <div class="d-flex align-items-center justify-content-end flex-nowrap w-100" style="gap:0.7rem; white-space:nowrap;">
          <a href="{{ route('login') }}" class="btn btn-primary btn-sm primary-shadow" style="min-width:110px;">Iniciar sesión</a>
          <a href="{{ route('password.request') }}" class="btn btn-link btn-sm" style="color: #6366f1; min-width:150px;">¿Olvidaste tu contraseña?</a>
        </div>
      </div>
    </nav>
  </div>
</section>
<!-- /navigation -->
