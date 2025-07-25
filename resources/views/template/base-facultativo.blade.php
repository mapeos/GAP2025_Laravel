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
  <title>@yield('title')</title>

  <!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- SEO Meta Tags -->
  <meta
    name="description"
    content="Backoffice para la gestion de alumnos" />
  <meta name="author" content="" />
  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="Gestion academia" />
  <meta
    property="og:description"
    content="Plataforma de gestion de alumnos, profesores y cursos" />
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
  <script>
    const theme = localStorage.getItem("theme");
    if (theme) document.documentElement.setAttribute("data-bs-theme", theme);
  </script>
  <!-- Source Sans 3 from Google Fonts -->
  <link
    href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap"
    rel="stylesheet" />

  @stack('css')


  <!-- Page CSS -->
  <!-- Component CSS -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Chart.js global -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
  <!-- FullCalendar global -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.11/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.11/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.11/index.global.min.js"></script>
  <!-- Bootstrap JS para el modal (si usas modales en dashboard) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <div class="wrapper">
    <!-- Overlay -->
    <div class="sidebar-overlay"></div>
    <!-- Sidebar -->
    <aside class="sidebar">
      <!-- Sticky Header -->
      <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">

          <!-- titulo de dashboard -->

           <!-- 
            puedes cambialo a nombre del usuario o otra cosa, es temporal
           -->

          <span class="mb-0 opacity-80">Facultativo dashboard</span>
          
        </a>
        <!-- Sidebar Toggle Button -->
        <div class="toggle-mini ms-auto">
          <button id="toggle-mini-button" class="btn btn-icon bg-body border-0" title="Toggle Minibar">
            <i class="ri-arrow-left-double-line opacity-75 fs-4"></i>
          </button>
        </div>
      </div>

      <!-- Scrollable Navigation -->
      <div class="sidebar-content" data-simplebar>
        @include('template.partials.sidebar-menu')
      </div>

      <!-- Sticky Footer NO SE VA A USAR-->
       <!--s
      <div class="sidebar-footer mt-2">
        @include('template.partials.sidebar-footer-user')
      </div>
      -->
    </aside>

    <!-- Main Content -->
    <main class="main-content px-2">
      <!-- Top Navigation -->
      <div class="topbar container">
        <nav class="d-flex align-items-center justify-content-between py-2">
          <!-- Left section -->
          <div class="d-flex align-items-center gap-4">
            <button class="btn sidebar-toggle d-md-block d-lg-block d-xl-none p-0 border-0" id="sidebar-toggle">
              <i class="ri-menu-line fs-5"></i>
            </button>
            <!-- Search Modal Toggle Button -->
            <button
              class="btn search-toggle w-100 text-start d-flex align-items-center gap-2 border shadow-none"
              id="search-toggle"
              data-bs-toggle="modal"
              data-bs-target="#searchModal"
              type="button">
              <i class="ri-search-line"></i> <span class="search-placeholder d-none d-sm-inline">Search </span>
              <span class="search-shortcut d-none d-sm-inline">Ctrl + K</span>
            </button>
          </div>
          <!-- Right section -->
          <div class="d-flex align-items-center gap-4">
            @include('template.partials.top-right-buttons')
          </div>

        </nav>
      </div>
      <!-- User Profile Offcanvas -->
      <div
        class="offcanvas offcanvas-end user-profile-offcanvas"
        tabindex="-1"
        id="userProfileOffcanvas"
        aria-labelledby="userProfileOffcanvasLabel">
        @include('template.partials.user-offcanvas')
      </div>
      <!-- Page Content -->
      <div class="content-wrapper py-4">
        <div class="container">
          <div class="page-header-container mb-4 border-bottom pb-2">
            <!-- First Row: Title and Actions -->
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="d-flex align-items-center">
                <h1 class="h3 mb-0">@yield('title-page')</h1>
              </div>
            </div>
            <!-- Second Row: Breadcrumbs -->
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item "><a href="{{ route('admin.dashboard') }}" class="text-decoration-none"> Dashboard </a></li>
                @yield('breadcrumb')
              </ol>
            </nav>
          </div>
          <div class="row">
            @yield('content')

          </div>
        </div>
      </div>
      <footer class="footer">
        <div class="container">
          <div class="row">
            <div class="col-md-6">
              <p class="mb-0">Copyright © {{ date('Y') }} <a href="#">Eat Pigeons</a>. Cook pigeons the way you like them.</p>
            </div>
            <div class="col-md-6 text-md-end">
              <p class="mb-0">Version 1.0.7</p>
            </div>
          </div>
        </div>
      </footer>
      @include('template.partials.search-modal')

    </main>
  </div>

  <!-- All Scripts -->
  <script>
    // Handle sidebar collapsed state - removed localStorage loading
    const sidebarElement = document.querySelector(".sidebar");
    const mainContentElement = document.querySelector(".main-content");

    // Handle sidebar-mini state
    const sidebarMini = localStorage.getItem("sidebar-mini");
    if (sidebarMini == "true") {
      sidebarElement.classList.add("sidebar-mini");
      mainContentElement.classList.add("expanded-mini");

      // Change the toggle-mini button icon
      const toggleMiniButton = document.getElementById("toggle-mini-button");
      toggleMiniButton.innerHTML = '<i class="ri-arrow-right-double-line opacity-75"></i>';

      const navItems = document.querySelectorAll(".nav-tree .has-submenu.open");
      navItems.forEach((item) => {
        item.classList.remove("open");
        // Find .ri-arrow-right-s-line and remove its inline transform style
        const chevron = item.querySelector(".ri-arrow-right-s-line");
        if (chevron) {
          chevron.style.transform = ""; // Reset the 'transform' style
        }
      });
    }
  </script>
  <!-- Main JS -->
  <script src="{{ asset('admin/js/main.js') }}" type="module"></script>

  @stack('js')


  <!-- Component JS -->
  <!-- Auto cerrar alertas después de 5 segundos -->
  <script>
    setTimeout(function() {
      document.querySelectorAll('.alert-dismissible').forEach(function(alertEl) {
        let alert = bootstrap.Alert.getOrCreateInstance(alertEl);
        alert.close();
      });
    }, 5000); // 5000 milisegundos = 5 segundos
  </script>
</body>

</html>