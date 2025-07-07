<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diploma - {{ $curso->titulo }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: white;
            width: 100%;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .diploma {
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f1f3f4 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        /* Marco principal */
        .diploma-frame {
            width: 90%;
            height: 85%;
            background: white;
            border: 12px solid #2c3e50;
            border-radius: 25px;
            position: relative;
            box-shadow: 
                0 0 0 4px #e74c3c,
                0 20px 40px rgba(0, 0, 0, 0.15),
                inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }
        
        /* Patrón de fondo */
        .diploma-frame::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(231, 76, 60, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(52, 152, 219, 0.03) 0%, transparent 50%),
                linear-gradient(45deg, transparent 48%, rgba(231, 76, 60, 0.02) 50%, transparent 52%);
            background-size: 100% 100%, 100% 100%, 20px 20px;
            pointer-events: none;
        }
        
        /* Esquinas decorativas */
        .corner {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 4px solid #e74c3c;
        }
        
        .corner-tl {
            top: 30px;
            left: 30px;
            border-right: none;
            border-bottom: none;
            border-radius: 25px 0 0 0;
        }
        
        .corner-tr {
            top: 30px;
            right: 30px;
            border-left: none;
            border-bottom: none;
            border-radius: 0 25px 0 0;
        }
        
        .corner-bl {
            bottom: 30px;
            left: 30px;
            border-right: none;
            border-top: none;
            border-radius: 0 0 0 25px;
        }
        
        .corner-br {
            bottom: 30px;
            right: 30px;
            border-left: none;
            border-top: none;
            border-radius: 0 0 25px 0;
        }
        
        /* Contenido principal */
        .diploma-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            text-align: center;
        }
        
        /* Header */
        .diploma-header {
            margin-bottom: 40px;
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 2px;
        }
        
        .institution {
            font-size: 1.3rem;
            color: #7f8c8d;
            font-weight: 300;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 1rem;
            color: #95a5a6;
            font-weight: 400;
            letter-spacing: 1px;
        }
        
        /* Título principal */
        .diploma-title {
            font-family: 'Playfair Display', serif;
            font-size: 4.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 50px 0;
            line-height: 1;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.1);
            letter-spacing: 3px;
        }
        
        /* Cuerpo del diploma */
        .diploma-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 40px 0;
        }
        
        .diploma-text {
            font-size: 1.6rem;
            color: #34495e;
            line-height: 1.8;
            margin-bottom: 30px;
            max-width: 900px;
            font-weight: 400;
        }
        
        .congratulations {
            font-size: 2rem;
            color: #e74c3c;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .course-name {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            font-weight: 700;
            color: #e74c3c;
            margin: 30px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            line-height: 1.2;
            max-width: 900px;
        }
        
        /* Información del curso */
        .course-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin: 50px 0;
            width: 100%;
            max-width: 900px;
        }
        
        .info-item {
            text-align: center;
            padding: 25px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 2px solid rgba(231, 76, 60, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .info-label {
            font-size: 1rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .info-value {
            font-size: 1.3rem;
            color: #2c3e50;
            font-weight: 600;
        }
        
        /* Firmas */
        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            width: 100%;
            max-width: 900px;
        }
        
        .signature-item {
            text-align: center;
            padding: 20px;
        }
        
        .signature-line {
            width: 250px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #2c3e50, transparent);
            margin: 25px auto;
            border-radius: 2px;
        }
        
        .signature-name {
            font-size: 1.3rem;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .signature-title {
            font-size: 1rem;
            color: #7f8c8d;
            font-weight: 400;
        }
        
        /* Fecha */
        .diploma-date {
            position: absolute;
            bottom: 40px;
            right: 60px;
            font-size: 1.1rem;
            color: #7f8c8d;
            font-style: italic;
            font-weight: 400;
        }
        
        /* Sello de autenticidad */
        .seal {
            position: absolute;
            top: 50px;
            right: 50px;
            width: 120px;
            height: 120px;
            border: 4px solid #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(231, 76, 60, 0.05);
            font-size: 0.8rem;
            color: #e74c3c;
            font-weight: 600;
            text-align: center;
            line-height: 1.2;
            transform: rotate(15deg);
        }
        
        /* Número de diploma */
        .diploma-number {
            position: absolute;
            bottom: 40px;
            left: 60px;
            font-size: 1rem;
            color: #7f8c8d;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="diploma">
        <div class="diploma-frame">
            <!-- Esquinas decorativas -->
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
            
            <!-- Sello de autenticidad -->
            <div class="seal">
                SELLO<br>OFICIAL<br>ACADEMIA<br>GAP
            </div>
            
            <div class="diploma-content">
                <!-- Header -->
                <div class="diploma-header">
                    <div class="logo">ACADEMIA GAP</div>
                    <div class="institution">Centro de Formación Profesional</div>
                    <div class="subtitle">Educación de Calidad • Formación Integral</div>
                </div>
                
                <!-- Título del diploma -->
                <div class="diploma-title">DIPLOMA</div>
                
                <!-- Cuerpo del diploma -->
                <div class="diploma-body">
                    <div class="congratulations">¡FELICITACIONES!</div>
                    
                    <div class="diploma-text">
                        Se otorga el presente diploma a quien ha completado exitosamente el programa de formación:
                    </div>
                    
                    <div class="course-name">{{ $curso->titulo }}</div>
                    
                    <div class="diploma-text">
                        Este documento certifica que el participante ha demostrado dedicación, esfuerzo y compromiso 
                        en su proceso de aprendizaje, completando satisfactoriamente todos los módulos y evaluaciones 
                        correspondientes a este programa de formación profesional.
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
                        <div class="info-label">Duración Total</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($curso->fechaInicio)->diffInDays($curso->fechaFin) }} días</div>
                    </div>
                </div>
                
                <!-- Firmas -->
                <div class="signature-section">
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <div class="signature-name">Dr. Juan Carlos Pérez</div>
                        <div class="signature-title">Director Académico</div>
                    </div>
                    
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <div class="signature-name">Lic. María Elena García</div>
                        <div class="signature-title">Coordinadora de Formación</div>
                    </div>
                </div>
            </div>
            
            <!-- Fecha del diploma -->
            <div class="diploma-date">
                Emitido el {{ \Carbon\Carbon::now()->format('d/m/Y') }}
            </div>
            
            <!-- Número de diploma -->
            <div class="diploma-number">
                Diploma N°: {{ str_pad($curso->id, 4, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
            </div>
        </div>
    </div>
</body>
</html> 