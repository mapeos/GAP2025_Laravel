<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curso no encontrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header-bg {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .academy-logo {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="error-card">
                    <!-- Header -->
                    <div class="header-bg">
                        <div class="academy-logo">
                            <i class="ri-award-line me-2"></i>
                            ACADEMIA GAP
                        </div>
                        <h4 class="mb-0">Error de Verificación</h4>
                        <p class="mb-0 opacity-75">Curso no encontrado</p>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="p-4 text-center">
                        <div class="mb-4">
                            <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h5 class="text-danger mb-3">Curso no encontrado</h5>
                        
                        <p class="text-muted mb-4">
                            El curso con ID <strong>{{ $cursoId }}</strong> no existe o ha sido eliminado.
                        </p>
                        
                        <div class="alert alert-warning">
                            <i class="ri-information-line me-2"></i>
                            <strong>Posibles causas:</strong>
                            <ul class="mb-0 mt-2 text-start">
                                <li>El curso ha sido eliminado</li>
                                <li>El código QR es incorrecto</li>
                                <li>El diploma puede ser fraudulento</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="ri-home-line me-2"></i>
                                Visitar Academia GAP
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 