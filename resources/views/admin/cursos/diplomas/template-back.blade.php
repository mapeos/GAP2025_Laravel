<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diploma - {{ $curso->titulo }} (Dorso)</title>
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
            min-height: 100vh;
        }
        
        /* Header del dorso */
        .diploma-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .institution {
            font-size: 1rem;
            color: #7f8c8d;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        /* Título del dorso */
        .diploma-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin: 30px 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        /* Información detallada */
        .diploma-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 30px 0;
            width: 100%;
        }
        
        .detail-section {
            background: rgba(255, 255, 255, 0.8);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .detail-title {
            font-size: 1.2rem;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 5px;
        }
        
        .detail-item {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .detail-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .detail-value {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 600;
        }
        
        /* Código QR y verificación */
        .verification-section {
            text-align: center;
            margin: 40px 0;
        }
        
        .qr-code {
            width: 120px;
            height: 120px;
            background: #f8f9fa;
            border: 2px solid #2c3e50;
            border-radius: 10px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #7f8c8d;
        }
        
        .verification-text {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .verification-url {
            font-size: 0.8rem;
            color: #3498db;
            font-weight: 500;
        }
        
        /* Información adicional */
        .additional-info {
            background: rgba(52, 152, 219, 0.1);
            padding: 20px;
            border-radius: 15px;
            margin: 30px 0;
        }
        
        .additional-title {
            font-size: 1.1rem;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .additional-text {
            font-size: 0.9rem;
            color: #34495e;
            line-height: 1.6;
            text-align: justify;
        }
        
        /* Footer */
        .diploma-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        
        .footer-left {
            text-align: left;
        }
        
        .footer-right {
            text-align: right;
        }
        
        .footer-text {
            font-size: 0.8rem;
            color: #7f8c8d;
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .diploma-content {
                padding: 30px;
            }
            
            .diploma-details {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .diploma-title {
                font-size: 2rem;
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
            
            <!-- Título -->
            <div class="diploma-title">INFORMACIÓN ADICIONAL</div>
            
            <!-- Detalles del curso -->
            <div class="diploma-details">
                <div class="detail-section">
                    <div class="detail-title">Información del Curso</div>
                    <div class="detail-item">
                        <span class="detail-label">Código:</span>
                        <span class="detail-value">CUR-{{ str_pad($curso->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value">{{ ucfirst($curso->estado) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Plazas:</span>
                        <span class="detail-value">{{ $curso->plazas }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Precio:</span>
                        <span class="detail-value">{{ $curso->precio ? '€' . number_format($curso->precio, 2) : 'Gratuito' }}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-title">Descripción del Curso</div>
                    <div class="detail-item">
                        <span class="detail-label">Título:</span>
                        <span class="detail-value">{{ $curso->titulo }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Descripción:</span>
                        <span class="detail-value">{{ Str::limit($curso->descripcion, 100) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Horas:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($curso->fechaInicio)->diffInDays($curso->fechaFin) * 8 }} horas</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Culminado:</span>
                        <span class="detail-value">Sí</span>
                    </div>
                </div>
            </div>
            
            <!-- Verificación -->
            <div class="verification-section">
                <div class="qr-code">
                    QR CODE<br>
                    {{ $curso->id }}-{{ date('Ymd') }}
                </div>
                <div class="verification-text">Verificar autenticidad en:</div>
                <div class="verification-url">www.academiagap.com/verificar</div>
            </div>
            
            <!-- Información adicional -->
            <div class="additional-info">
                <div class="additional-title">Notas Importantes</div>
                <div class="additional-text">
                    Este diploma es un documento oficial que certifica la participación y finalización exitosa del curso. 
                    Para verificar su autenticidad, escanee el código QR o visite nuestra página web. 
                    Este documento no tiene validez legal sin la firma correspondiente del director académico.
                </div>
            </div>
            
            <!-- Footer -->
            <div class="diploma-footer">
                <div class="footer-left">
                    <div class="footer-text">Documento generado automáticamente</div>
                    <div class="footer-text">ID: {{ $curso->id }}-{{ date('YmdHis') }}</div>
                </div>
                <div class="footer-right">
                    <div class="footer-text">Página 2 de 2</div>
                    <div class="footer-text">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 