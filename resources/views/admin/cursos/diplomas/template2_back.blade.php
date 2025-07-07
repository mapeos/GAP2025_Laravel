<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diploma - Parte Trasera</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            width: 210mm;
            height: 297mm;
            font-family: 'Roboto', sans-serif;
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 210mm;
            height: 297mm;
            padding: 20mm;
            position: relative;
        }

        .identifier {
            text-align: right;
            font-size: 10pt;
            color: #666;
            margin-bottom: 10mm;
        }

        .section-container {
            display: flex;
            justify-content: space-between;
            height: calc(100% - 40mm); /* dejar espacio para top/bottom */
            gap: 10mm;
        }

        .section {
            width: 50%;
            font-size: 11pt;
            line-height: 1.6;
            text-align: justify;
        }

        .section h3 {
            font-size: 13pt;
            margin-bottom: 5mm;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2mm;
        }

        .footer-note {
            position: absolute;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            font-size: 9pt;
            text-align: center;
            color: #999;
        }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="identifier">
            Código: {{ now()->year }}/{{ $curso->id }}/{{ $curso->alumno->id ?? '0' }}
        </div>

        <div class="section-container">
            <div class="section">
                <h3>Descripción General</h3>
                <p>
                    Este diploma acredita la participación del alumno en el curso
                    <strong>"{{ $curso->titulo }}"</strong>, orientado a profesionales interesados en actualizar sus competencias.
                </p>
                <p>
                    El curso se desarrolló en modalidad {{ $curso->modalidad ?? 'presencial/online' }}, con un enfoque práctico y participativo.
                </p>
            </div>

            <div class="section">
                <h3>Contenidos</h3>
                <p>
                    {{ $curso->descripcion ?? 'Este curso ha abordado los siguientes contenidos: ...' }}
                </p>
            </div>
        </div>

        <div class="footer-note">
            Este documento forma parte de la certificación oficial del curso emitido por la entidad organizadora.
        </div>
    </div>
</body>
</html>
