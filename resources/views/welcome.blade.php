<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auria Academy</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Admin styles -->
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
    <!-- Feather icons -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);">
    <div style="background: white; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15); border-radius: 1.5rem; padding: 3rem 2.5rem; display: flex; flex-direction: column; align-items: center; min-width: 350px; max-width: 90vw;">
        <h1 style="font-size: 3.5rem; font-weight: bold; color: #6366f1; letter-spacing: 2px; margin-bottom: 1.5rem;">Auria Academy</h1>
        <p style="font-size: 1.2rem; color: #64748b; margin-bottom: 2.5rem; text-align: center;">Bienvenido a la plataforma de aprendizaje. Accede o regístrate para comenzar tu experiencia educativa.</p>
        <div style="display: flex; gap: 1rem; margin-bottom: 1.2rem;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.1rem; border-radius: 0.75rem;">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="btn btn-secondary" style="padding: 0.75rem 2rem; font-size: 1.1rem; border-radius: 0.75rem;">Registrarse</a>
        </div>
        <a href="{{ route('password.request') }}" class="btn btn-link" style="margin-bottom: 1.2rem; color: #6366f1;">¿Olvidaste tu contraseña?</a>
        <button id="google-login" class="btn btn-danger" type="button" style="padding: 0.7rem 2rem; font-size: 1.1rem; border-radius: 0.75rem; margin-bottom: 1.2rem;">
            <i data-feather="google" style="margin-right: 0.5rem;"></i> Iniciar sesión con Google
        </button>
        @if (session('error'))
            <div style="color: #ef4444; font-size: 1.1rem; margin-bottom: 1rem; text-align: center;">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-auth-compat.js"></script>
    <script>
      // Configuración de Firebase (reemplaza con tus datos reales)
      const firebaseConfig = {
        apiKey: "AIzaSyC-KaHfqWZYD2iUBF3CHDaPosCAIP9Cr9M",
        authDomain: "academia-95951.firebaseapp.com",
        projectId: "academia-95951",
        appId: "1:1096768697439:web:ae4af46fe7b8bfad2cd7ea",
        // ...otros datos si los tienes
      };
      firebase.initializeApp(firebaseConfig);

      document.getElementById('google-login').addEventListener('click', function() {
        const provider = new firebase.auth.GoogleAuthProvider();
        firebase.auth().signInWithPopup(provider)
          .then(async (result) => {
            const idToken = await result.user.getIdToken();
            // Envía el token a tu backend para autenticar al usuario en Laravel
            fetch('/login/firebase', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ id_token: idToken })
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                window.location.href = '/dashboard'; // O la ruta que corresponda
              } else {
                alert('Error al iniciar sesión con Google');
              }
            });
          })
          .catch((error) => {
            alert('Error de autenticación con Google');
            console.error(error);
          });
      });
    </script>
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
