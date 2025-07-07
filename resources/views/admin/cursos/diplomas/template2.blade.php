<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diploma</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            width: 297mm;
            height: 210mm;
            font-family: 'Roboto', sans-serif;
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .container {
            width: 297mm;
            height: 210mm;
            padding: 20mm;
            position: relative;
        }

        .header {
            height: 40mm;
            text-align: center;
            margin-bottom: 10mm;
        }

        .header img {
            height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .title {
            font-family: 'Playfair Display', serif;
            text-align: center;
            font-size: 28pt;
            font-weight: 700;
            margin-bottom: 10mm;
        }

        .subtitle {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 15mm;
        }

        .content {
            font-size: 13pt;
            text-align: center;
            margin-bottom: 20mm;
            line-height: 1.6;
        }

        .signature-area {
            position: absolute;
            bottom: 30mm;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30mm;
        }

        .signature-block {
            text-align: center;
            width: 40%;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 20mm;
        }

        .signature-label {
            margin-top: 5mm;
            font-size: 10pt;
            color: #555;
        }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('admin/img/placeholder-images/placeholder-image-180x240.png') }}" alt="Patrocinadores">
        </div>

        <div class="title">DIPLOMA CONCEDIDO A</div>
        <div class="subtitle">Nombre del Alumno</div>

        <div class="content">
            Por su participación y finalización satisfactoria del curso<br>
            <strong>{{ $curso->titulo }}</strong><br>
            celebrado del {{ \Carbon\Carbon::parse($curso->fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($curso->fechaFin)->format('d/m/Y') }}.
        </div>

        <div class="signature-area">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Coordinador Académico</div>
            </div>
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Director del Curso</div>
            </div>
        </div>
    </div>
</body>
</html>
