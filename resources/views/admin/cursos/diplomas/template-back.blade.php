<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diploma - {{ $curso->titulo }} (Reverso)</title>
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
            padding: 32px 32px 24px 32px; /* Menos padding */
        }
        
        /* Header */
        .diploma-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 2px;
        }
        
        .institution {
            font-size: 1.1rem;
            color: #7f8c8d;
            font-weight: 300;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 0.9rem;
            color: #95a5a6;
            font-weight: 400;
            letter-spacing: 1px;
        }
        
        /* Título del reverso */
        .diploma-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin: 18px 0 18px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 1px;
        }
        
        /* Información detallada */
        .diploma-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            margin: 18px 0 18px 0;
            flex: 1;
        }
        
        .detail-section {
            background: rgba(255, 255, 255, 0.92);
            padding: 16px 14px;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
            border: 1.5px solid rgba(231, 76, 60, 0.09);
            backdrop-filter: blur(6px);
        }
        
        .detail-title {
            font-size: 1.1rem;
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 10px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 6px;
            text-align: center;
            font-family: 'Playfair Display', serif;
        }
        
        .detail-item {
            margin-bottom: 7px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px solid rgba(231, 76, 60, 0.08);
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-size: 0.92rem;
            color: #7f8c8d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 0.98rem;
            color: #2c3e50;
            font-weight: 600;
            text-align: right;
        }
        
        /* Código QR y verificación */
        .verification-section {
            text-align: center;
            margin: 18px 0 18px 0;
            background: rgba(52, 152, 219, 0.04);
            padding: 14px 8px 10px 8px;
            border-radius: 12px;
            border: 1.5px solid rgba(52, 152, 219, 0.13);
        }
        
        .verification-title {
            font-size: 1.05rem;
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 8px;
            font-family: 'Playfair Display', serif;
        }
        
        .qr-code {
            width: 120px;
            height: 135px;
            background: white;
            border: 3px solid #2c3e50;
            border-radius: 12px;
            margin: 0 auto 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #2c3e50;
            font-weight: 500;
            text-align: center;
            line-height: 1.1;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
            padding: 6px 4px 4px 4px;
            overflow: hidden;
        }
        
        .verification-text {
            font-size: 0.92rem;
            color: #7f8c8d;
            margin-bottom: 6px;
            font-weight: 500;
        }
        
        .verification-url {
            font-size: 0.85rem;
            color: #3498db;
            font-weight: 600;
            text-decoration: none;
        }
        
        /* Información adicional */
        .additional-info {
            background: rgba(231, 76, 60, 0.04);
            padding: 12px 10px;
            border-radius: 10px;
            margin: 14px 0 10px 0;
            border: 1.5px solid rgba(231, 76, 60, 0.13);
        }
        
        .additional-title {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 7px;
            text-align: center;
            font-family: 'Playfair Display', serif;
        }
        
        .additional-text {
            font-size: 0.92rem;
            color: #34495e;
            line-height: 1.4;
            text-align: justify;
            font-weight: 400;
        }
        
        /* Footer */
        .diploma-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1.5px solid rgba(231, 76, 60, 0.13);
        }
        
        .footer-left {
            text-align: left;
        }
        
        .footer-right {
            text-align: right;
        }
        
        .footer-text {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 400;
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
        
        /* Fecha */
        .diploma-date {
            position: absolute;
            bottom: 40px;
            right: 60px;
            font-size: 1rem;
            color: #7f8c8d;
            font-style: italic;
            font-weight: 400;
        }
        
        /* Sello de autenticidad */
        .seal {
            position: absolute;
            top: 50px;
            right: 50px;
            width: 100px;
            height: 100px;
            border: 3px solid #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(231, 76, 60, 0.05);
            font-size: 0.7rem;
            color: #e74c3c;
            font-weight: 600;
            text-align: center;
            line-height: 1.2;
            transform: rotate(-15deg);
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
                SELLO<br>OFICIAL<br>REVERSO<br>GAP
            </div>
            
            <div class="diploma-content">
                <!-- Header -->
                <div class="diploma-header">
                    <div class="logo">ACADEMIA GAP</div>
                    <div class="institution">Centro de Formación Profesional</div>
                    <div class="subtitle">Educación de Calidad • Formación Integral</div>
                </div>
                
                <!-- Título del reverso -->
                <div class="diploma-title">INFORMACIÓN ADICIONAL</div>
                
                <!-- Información detallada -->
                <div class="diploma-details">
                    <div class="detail-section">
                        <div class="detail-title">Datos del Curso</div>
                        <div class="detail-item">
                            <span class="detail-label">Nombre del Curso</span>
                            <span class="detail-value">{{ $curso->titulo }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Código del Curso</span>
                            <span class="detail-value">{{ str_pad($curso->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Fecha de Inicio</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Fecha de Finalización</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Duración Total</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($curso->fechaInicio)->diffInDays($curso->fechaFin) }} días</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Estado del Curso</span>
                            <span class="detail-value">{{ $curso->estado ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="detail-title">Datos del Diploma</div>
                        <div class="detail-item">
                            <span class="detail-label">Número de Diploma</span>
                            <span class="detail-value">{{ str_pad($curso->id, 4, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Fecha de Emisión</span>
                            <span class="detail-value">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tipo de Certificación</span>
                            <span class="detail-value">Diploma Profesional</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nivel de Formación</span>
                            <span class="detail-value">Formación Continua</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Validez</span>
                            <span class="detail-value">Permanente</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Institución</span>
                            <span class="detail-value">Academia GAP</span>
                        </div>
                    </div>
                </div>
                
                <!-- Código QR y verificación -->
                <div class="verification-section">
                    <div class="verification-title">Verificación de Autenticidad</div>
                    <div class="qr-code">
                        @if(isset($qrCode) && $qrCode)
                            <div style="width: 100px; height: 100px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                <img src='{{ $qrCode }}' alt='Código QR para verificación' style='max-width: 100%; max-height: 100%; width: auto; height: auto; display: block; object-fit: contain; margin: 0 auto;' />
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; width: 100%;">
                                <span style="font-weight: 700; font-size: 1rem; color: #2c3e50;">CÓDIGO QR</span>
                            </div>
                        @endif
                    </div>
                    <div class="verification-text">Para verificar la autenticidad de este diploma, escanee el código QR o visite:</div>
                    <div class="verification-url">{{ config('app.url') }}/cursos/{{ $curso->id }}</div>
                </div>
                
                <!-- Información adicional -->
                <div class="additional-info">
                    <div class="additional-title">Descripción del Programa</div>
                    <div class="additional-text">
                        {{ $curso->descripcion ?: 'Este programa de formación profesional ha sido diseñado para proporcionar conocimientos teóricos y prácticos especializados, desarrollando competencias profesionales que permiten a los participantes desempeñarse eficazmente en su área de especialización. El curso incluye evaluaciones continuas, proyectos prácticos y una evaluación final que garantiza la adquisición de los conocimientos y habilidades necesarias para la certificación.' }}
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="diploma-footer">
                    <div class="footer-left">
                        <div class="footer-text">Este documento es oficial y tiene validez legal</div>
                    </div>
                    <div class="footer-right">
                        <div class="footer-text">Academia GAP - Centro de Formación Profesional</div>
                    </div>
                </div>
            </div>
            
            <!-- Número de diploma -->
            <div class="diploma-number">
                Diploma N°: {{ str_pad($curso->id, 4, '0', STR_PAD_LEFT) }}-{{ date('Y') }}
            </div>
            
            <!-- Fecha del diploma -->
            <div class="diploma-date">
                Emitido el {{ \Carbon\Carbon::now()->format('d/m/Y') }}
            </div>
        </div>
    </div>
</body>
</html> 