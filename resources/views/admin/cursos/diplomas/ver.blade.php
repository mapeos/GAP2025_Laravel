<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diploma - {{ $persona->nombre }} - {{ $curso->titulo }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .fullscreen-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .header-subtitle {
            font-size: 1rem;
            color: #7f8c8d;
            margin: 0;
        }
        
        .header-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn-group {
            display: flex;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .btn-group .btn {
            border: none;
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-group .btn.active {
            background: #007bff;
            color: white;
        }
        
        .btn-group .btn:not(.active) {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .btn-group .btn:hover:not(.active) {
            background: #e9ecef;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .diploma-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 20px 0;
        }
        
        .diploma-view {
            display: none;
            width: 100%;
            padding: 20px 0;
        }
        
        .diploma-view.active {
            display: block;
        }
        
        .diploma-view .diploma {
            transform: scale(1);
            transform-origin: top center;
            transition: transform 0.3s ease;
            margin: 0 auto;
        }
        
        .diploma-view .diploma:hover {
            transform: scale(1.02);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header-left {
                flex-direction: column;
                gap: 10px;
            }
            
            .header-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .diploma-view .diploma {
                transform: scale(0.8);
            }
            
            .diploma-view .diploma:hover {
                transform: scale(0.85);
            }
        }
        
        @media (max-width: 576px) {
            .diploma-view .diploma {
                transform: scale(0.6);
            }
            
            .diploma-view .diploma:hover {
                transform: scale(0.65);
            }
        }
        
        /* Animaciones */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Spinner de carga */
        .loading-spinner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .spinner-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .spinner-text {
            color: #333;
            font-size: 1.1rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Spinner de carga -->
    <div id="loadingSpinner" class="loading-spinner">
        <div class="spinner-content">
            <div class="spinner"></div>
            <div class="spinner-text">Descargando Diploma...</div>
            <button type="button" class="btn btn-outline-secondary btn-sm mt-3" onclick="hideSpinner()">
                <i class="ri-close-line me-1"></i>
                Cancelar
            </button>
        </div>
    </div>
    
    <div class="fullscreen-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div>
                        <h1 class="header-title">
                            <i class="ri-award-line me-2 text-primary"></i>
                            Diploma del Participante
                        </h1>
                        <p class="header-subtitle">
                            <strong>{{ $persona->nombre }} {{ $persona->apellido1 }} {{ $persona->apellido2 }}</strong> - {{ $curso->titulo }}
                        </p>
                    </div>
                </div>
                
                <div class="header-controls">
                    <div class="btn-group">
                        <button type="button" class="btn active" onclick="showFront()">
                            <i class="ri-file-text-line me-1"></i>
                            Frente
                        </button>
                        <button type="button" class="btn" onclick="showBack()">
                            <i class="ri-file-text-line me-1"></i>
                            Dorso
                        </button>
                    </div>
                    
                    <button type="button" 
                            class="btn btn-success"
                            onclick="downloadPDF()">
                        <i class="ri-download-line me-2"></i>
                        Descargar Diploma
                    </button>
                    
                    <button type="button" 
                            class="btn btn-primary"
                            onclick="window.print()">
                        <i class="ri-printer-line me-2"></i>
                        Imprimir
                    </button>
                    
                    <button type="button" 
                            class="btn btn-outline-secondary"
                            onclick="window.close()">
                        <i class="ri-close-line me-2"></i>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Contenido principal -->
        <div class="main-content">
            <div class="diploma-container">
                <!-- Vista del frente -->
                <div id="diploma-front" class="diploma-view active fade-in">
                    @include('admin.cursos.diplomas.template2', ['curso' => $curso, 'persona' => $persona])
                </div>
                
                <!-- Vista del dorso -->
                <div id="diploma-back" class="diploma-view fade-in">
                    @include('admin.cursos.diplomas.template-back', ['curso' => $curso, 'persona' => $persona, 'qrCode' => $qrCode])
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variable global para la URL de descarga
        const downloadUrl = "{{ route('admin.cursos.diploma.participante.descargar', [$curso->id, $persona->id]) }}";

        function showFront() {
            // Ocultar todas las vistas
            document.querySelectorAll('.diploma-view').forEach(view => {
                view.classList.remove('active');
            });
            
            // Mostrar frente
            document.getElementById('diploma-front').classList.add('active');
            
            // Actualizar botones
            updateButtons(event.target);
        }
        
        function showBack() {
            // Ocultar todas las vistas
            document.querySelectorAll('.diploma-view').forEach(view => {
                view.classList.remove('active');
            });
            
            // Mostrar dorso
            document.getElementById('diploma-back').classList.add('active');
            
            // Actualizar botones
            updateButtons(event.target);
        }
        
        function updateButtons(clickedButton) {
            // Remover clase active de todos los botones
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Agregar clase active al botón clickeado
            clickedButton.classList.add('active');
        }
        
        // Atajos de teclado
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowLeft') {
                showFront();
            } else if (event.key === 'ArrowRight') {
                showBack();
            } else if (event.key === 'Escape') {
                window.close();
            }
        });
        
        // Función para descargar PDF
        function downloadPDF() {
            // Mostrar spinner de carga
            const spinner = document.getElementById('loadingSpinner');
            spinner.style.display = 'flex';
            
            // Función para ocultar spinner
            function hideSpinner() {
                spinner.style.display = 'none';
            }
            
            // Usar fetch para detectar cuando el PDF está listo
            fetch(downloadUrl)
                .then(response => {
                    if (response.ok) {
                        return response.blob();
                    }
                    throw new Error('Error al generar el PDF');
                })
                .then(blob => {
                    // Crear URL del blob
                    const url = window.URL.createObjectURL(blob);
                    
                    // Crear enlace de descarga
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'diploma_{{ $persona->nombre }}_{{ $curso->titulo }}.pdf';
                    
                    // Descargar el archivo
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Limpiar URL del blob
                    window.URL.revokeObjectURL(url);
                    
                    // Ocultar spinner después de que se inicie la descarga
                    setTimeout(hideSpinner, 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideSpinner();
                    alert('Error al generar el PDF. Inténtelo de nuevo.');
                });
            
            // Respaldo: ocultar spinner después de 15 segundos máximo
            setTimeout(function() {
                if (spinner.style.display === 'flex') {
                    hideSpinner();
                }
            }, 15000);
        }
        
        // Función para ocultar spinner manualmente
        function hideSpinner() {
            const spinner = document.getElementById('loadingSpinner');
            spinner.style.display = 'none';
        }
    </script>
</body>
</html> 