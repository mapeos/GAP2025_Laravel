<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GAP2025</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Admin styles -->
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
    <!-- Feather icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh;">
    <h1 style="font-size: 4rem; font-weight: bold;">GAP2025</h1>
    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
        <a href="{{ route('login') }}" class="btn btn-primary">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="btn btn-secondary">Registrarse</a>
    </div>
    <div style="margin-top: 1rem;">
        <a href="{{ route('password.request') }}" class="btn btn-link">¿Olvidaste tu contraseña?</a>
    </div>

    @if (session('error'))
        <div style="color: red; font-size: 1.2rem; margin-bottom: 1rem;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="{{ asset('admin/js/main.js') }}"></script>
    <script>
        // Initialize Feather icons
        feather.replace();

        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });

        // Navbar scroll behavior
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 0) {
                navbar.classList.add('navbar-sticky', 'bg-white');
                navbar.classList.remove('navbar-light');
            } else {
                navbar.classList.remove('navbar-sticky', 'bg-white');
                navbar.classList.add('navbar-light');
            }
        });
    </script>
</body>
</html>
