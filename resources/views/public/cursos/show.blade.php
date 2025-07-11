<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Diploma - {{ $curso->titulo }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .qr-scanned {
            background: #e8f5e8;
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .info-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .academy-logo {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .diploma-verified {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="verification-card">
                    <!-- Header -->
                    <div class="header-bg">
                        <div class="academy-logo">
                            <i class="ri-award-line me-2"></i>
                            ACADEMIA GAP
                        </div>
                        <h4 class="mb-0">Verificación de Diploma</h4>
                        <p class="mb-0 opacity-75">Código QR escaneado correctamente</p>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="p-4">
                        <!-- Mensaje de verificación -->
                        <div class="diploma-verified">
                            <i class="ri-check-circle-line me-2"></i>
                            <strong>Diploma Verificado</strong>
                            <br>
                            <small>Este código QR corresponde a un diploma oficial emitido por Academia GAP</small>
                        </div>
                        
                        <!-- Información del curso -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="ri-book-open-line me-2"></i>
                                Información del Curso
                            </h5>
                            
                            <div class="info-item">
                                <strong>Título del curso:</strong>
                                <div class="text-muted">{{ $curso->titulo }}</div>
                            </div>
                            
                            @if($curso->descripcion)
                            <div class="info-item">
                                <strong>Descripción:</strong>
                                <div class="text-muted">{{ $curso->descripcion }}</div>
                            </div>
                            @endif
                            
                            <div class="info-item">
                                <strong>Fechas:</strong>
                                <div class="text-muted">
                                    {{ $curso->fechaInicio?->format('d/m/Y') }} - {{ $curso->fechaFin?->format('d/m/Y') }}
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <strong>Estado del curso:</strong>
                                <div>
                                    @if($esActivo)
                                        <span class="badge bg-success status-badge">
                                            <i class="ri-check-line me-1"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary status-badge">
                                            <i class="ri-time-line me-1"></i> {{ ucfirst($curso->estado) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <strong>Plazas:</strong>
                                <div class="text-muted">
                                    {{ $totalInscritos }} inscritos de {{ $curso->plazas }} plazas
                                    @if($curso->plazas > 0)
                                        ({{ $porcentajeOcupacion }}% ocupación)
                                    @endif
                                </div>
                            </div>
                            
                            @if($curso->precio)
                            <div class="info-item">
                                <strong>Precio:</strong>
                                <div class="text-muted">{{ number_format($curso->precio, 2) }} €</div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">
                                <i class="ri-information-line me-2"></i>
                                Información Adicional
                            </h6>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="h4 text-primary mb-1">{{ $totalInscritos }}</div>
                                        <small class="text-muted">Participantes</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="h4 text-success mb-1">{{ $plazasDisponibles }}</div>
                                        <small class="text-muted">Plazas disponibles</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="text-center pt-3 border-top">
                            <small class="text-muted">
                                <i class="ri-shield-check-line me-1"></i>
                                Diploma oficial verificado por Academia GAP
                            </small>
                            <br>
                            <small class="text-muted">
                                Fecha de verificación: {{ $fechaActual->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="btn btn-outline-light">
                        <i class="ri-home-line me-2"></i>
                        Visitar Academia GAP
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 