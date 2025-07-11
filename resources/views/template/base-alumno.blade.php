<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <!-- Resource Hints -->
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- SEO Meta Tags -->
    <meta
      name="description"
      content="Backoffice para la gestion de alumnos"
    />
    <meta name="author" content="" />
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Gestion academia" />
    <meta
      property="og:description"
      content="Plataforma de gestion de alumnos, profesores y cursos"
    />
    <meta property="og:image" content="{{ asset('/admin/img/gap_ico.png') }}" />
    <meta property="og:type" content="website" />
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" sizes="32x32" />
    <link rel="icon" href="/icon.svg" type="image/svg+xml" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/manifest.webmanifest" />
    <!-- Admin CSS -->
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet" />
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet" />
    <!-- Source Sans 3 from Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap"
      rel="stylesheet"
    />

    @stack('css')

     <!-- CSS compilado por Vite -->
    @vite('resources/css/app.css')

    <!-- JS compilado por Vite -->
    @vite('resources/js/app.js')
    
    <!-- FullCalendar global -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.11/index.global.min.js"></script>
  </head>
  <body>
    <style>
      /* Forzar 100% de ancho y eliminar márgenes/paddings heredados */
      main.main-content, .content-wrapper, .container-fluid, .row {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
      }
      .content-wrapper {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
      }
      /* Eliminar cualquier espacio lateral en pantallas grandes */
      body, html {
        overflow-x: hidden;
      }
      /* Padding horizontal para la topbar en todos los tamaños */
      .topbar {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
      }
      @media (max-width: 576px) {
        .topbar {
          padding-left: 0.75rem !important;
          padding-right: 0.75rem !important;
        }
      }
      /* Padding horizontal para el contenido principal de alumno */
      main.main-content, .content-wrapper, .container-fluid {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
      }
      @media (max-width: 576px) {
        main.main-content, .content-wrapper, .container-fluid {
          padding-left: 0.75rem !important;
          padding-right: 0.75rem !important;
        }
      }
    </style>
    <!-- Top Navigation siempre visible y funcional -->
    @include('template.partials.topbar')
    <!-- Main Content sin sidebar ni wrapper -->
    <main class="main-content px-2">
      <div class="content-wrapper py-4">
        <div class="container-fluid">
          <div class="page-header-container mb-4 border-bottom pb-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="d-flex align-items-center">
                <h1 class="h3 mb-0">@yield('title-page')</h1>
              </div>
            </div>
            @yield('breadcrumbs')
          </div>
          <div class="row">
            @yield('content')
          </div>
        </div>
      </div>
    </main>
    <!-- User Profile Offcanvas fuera de cualquier contenedor -->
    <div
      class="offcanvas offcanvas-end user-profile-offcanvas"
      tabindex="-1"
      id="userProfileOffcanvas"
      aria-labelledby="userProfileOffcanvasLabel">
      @include('template.partials.user-offcanvas-alumno')
    </div>
    @include('template.partials.footer')
    @stack('js')
    <!-- Bootstrap JS (asegúrate de que está cargado) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Script para alternar modo claro/oscuro
      document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        const sunIcon = '<i class="ri-sun-line fs-5"></i>';
        const moonIcon = '<i class="ri-moon-line fs-5"></i>';
        function setTheme(theme) {
          html.setAttribute('data-bs-theme', theme);
          localStorage.setItem('theme', theme);
          if (themeToggle) {
            if (theme === 'dark') {
              themeToggle.innerHTML = sunIcon;
            } else {
              themeToggle.innerHTML = moonIcon;
            }
          }
        }
        // Inicializar icono según el tema actual
        let currentTheme = localStorage.getItem('theme') || 'light';
        setTheme(currentTheme);
        if (themeToggle) {
          themeToggle.addEventListener('click', function() {
            currentTheme = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            setTheme(currentTheme);
          });
        }
      });
    </script>
  </body>
</html>
