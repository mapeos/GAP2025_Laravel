<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diploma - {{ $curso->titulo }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .diploma {
            background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
            width: 100%;
            max-width: 1200px;
            aspect-ratio: 1.414; /* A4 ratio */
            position: relative;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 8px solid #2c3e50;
        }
        
        /* Borde decorativo */
        .diploma::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #e74c3c;
            border-radius: 15px;
            pointer-events: none;
        }
        
        /* Fondo con patrón sutil */
        .diploma::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(231, 76, 60, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(52, 152, 219, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .diploma-content {
            position: relative;
            z-index: 1;
            padding: 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            min-height: 100vh;
        }
        
        /* Logo/Header */
        .diploma-header {
            margin-bottom: 40px;
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .institution {
            font-size: 1.2rem;
            color: #7f8c8d;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        /* Título principal */
        .diploma-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 40px 0;
            line-height: 1.2;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        /* Contenido del diploma */
        .diploma-body {
            margin: 40px 0;
        }
        
        .diploma-text {
            font-size: 1.4rem;
            color: #34495e;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 800px;
        }
        
        .course-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #e74c3c;
            margin: 20px 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        /* Información del curso */
        .course-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
            width: 100%;
            max-width: 800px;
        }
        
        .info-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.1);
        }
        
        .info-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        /* Firma */
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            width: 100%;
            max-width: 800px;
        }
        
        .signature-item {
            text-align: center;
            padding: 20px;
        }
        
        .signature-line {
            width: 200px;
            height: 2px;
            background: #2c3e50;
            margin: 20px auto;
        }
        
        .signature-name {
            font-size: 1.1rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .signature-title {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        /* Elementos decorativos */
        .decorative-corner {
            position: absolute;
            width: 60px;
            height: 60px;
            border: 3px solid #e74c3c;
        }
        
        .corner-tl {
            top: 40px;
            left: 40px;
            border-right: none;
            border-bottom: none;
        }
        
        .corner-tr {
            top: 40px;
            right: 40px;
            border-left: none;
            border-bottom: none;
        }
        
        .corner-bl {
            bottom: 40px;
            left: 40px;
            border-right: none;
            border-top: none;
        }
        
        .corner-br {
            bottom: 40px;
            right: 40px;
            border-left: none;
            border-top: none;
        }
        
        /* Fecha */
        .diploma-date {
            position: absolute;
            bottom: 40px;
            right: 60px;
            font-size: 1rem;
            color: #7f8c8d;
            font-style: italic;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .diploma-content {
                padding: 30px;
            }
            
            .diploma-title {
                font-size: 2rem;
            }
            
            .course-name {
                font-size: 1.5rem;
            }
            
            .course-info {
                flex-direction: column;
                align-items: center;
            }
            
            .signature-section {
                flex-direction: column;
                gap: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="diploma">
        <!-- Esquinas decorativas -->
        <div class="decorative-corner corner-tl"></div>
        <div class="decorative-corner corner-tr"></div>
        <div class="decorative-corner corner-bl"></div>
        <div class="decorative-corner corner-br"></div>
        
        <div class="diploma-content">
            <!-- Header -->
            <div class="diploma-header">
                <div class="logo">Academia GAP</div>
                <div class="institution">Centro de Formación Profesional</div>
            </div>
            
            <!-- Título del diploma -->
            <div class="diploma-title">DIPLOMA</div>
            
            <!-- Cuerpo del diploma -->
            <div class="diploma-body">
                <div class="diploma-text">
                    ¡FELICITACIONES!
                </div>
                
                <div class="diploma-text">
                    Has completado exitosamente el curso:
                </div>
                
                <div class="course-name">{{ $curso->titulo }}</div>
                
                <div class="diploma-text">
                    Este diploma certifica que has demostrado dedicación, esfuerzo y compromiso en tu proceso de aprendizaje, 
                    completando satisfactoriamente todos los módulos y evaluaciones correspondientes a este programa de formación.
                </div>
            </div>
            
            <!-- Información del curso -->
            <div class="course-info">
                <div class="info-item">
                    <div class="info-label">Fecha de Inicio</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Fecha de Finalización</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Duración</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($curso->fechaInicio)->diffInDays($curso->fechaFin) }} días</div>
                </div>
            </div>
            
            <!-- Firmas -->
            <div class="signature-section">
                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div class="signature-name">Dr. Juan Pérez</div>
                    <div class="signature-title">Director Académico</div>
                </div>
                
                <div class="signature-item">
                    <div class="signature-line"></div>
                    <div class="signature-name">Lic. María García</div>
                    <div class="signature-title">Coordinadora de Cursos</div>
                </div>
            </div>
        </div>
        
        <!-- Fecha del diploma -->
        <div class="diploma-date">
            Emitido el {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>
    </div>
</body>
</html> 