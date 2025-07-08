<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notificación' }}</title>
    <!-- Source Sans 3 from Google Fonts - Same as admin -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet" />
    <style>
        /* CSS Variables matching admin theme */
        :root {
            --bs-primary: #7533f9;
            --bs-blue: #3f78e0;
            --bs-success: #45c4a0;
            --bs-warning: #fab758;
            --bs-danger: #e2626b;
            --bs-secondary: #6c757d;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;
            --bs-body-color: #212529;
            --bs-body-bg: #fff;
            --bs-border-color: #dee2e6;
        }

        body {
            font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: var(--bs-body-color);
            margin: 0;
            padding: 20px;
            background-color: var(--bs-light);
            font-size: 16px;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--bs-body-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .email-header {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-blue) 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .email-header .logo {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
        }

        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            opacity: 0.95;
        }

        .email-body {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: var(--bs-primary);
            margin-bottom: 25px;
        }
        .content {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
            color: var(--bs-body-color);
        }

        .content p {
            margin-bottom: 16px;
        }

        .action-button {
            text-align: center;
            margin: 35px 0;
        }

        .btn-primary {
            display: inline-block;
            padding: 12px 28px;
            background-color: var(--bs-primary);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(117, 51, 249, 0.3);
        }

        .btn-primary:hover {
            background-color: #5d2bc7;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(117, 51, 249, 0.4);
            color: #ffffff;
        }

        .email-footer {
            background-color: var(--bs-light);
            padding: 30px;
            border-top: 1px solid var(--bs-border-color);
            text-align: center;
        }

        .footer-text {
            font-size: 16px;
            color: var(--bs-body-color);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .app-info {
            background-color: rgba(117, 51, 249, 0.05);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--bs-primary);
        }

        .app-info p {
            margin: 8px 0;
            font-size: 14px;
            color: var(--bs-secondary);
        }

        .app-info p:first-child {
            font-weight: 600;
            color: var(--bs-primary);
        }

        /* Responsive design */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .email-header {
                padding: 25px 20px;
            }

            .email-body {
                padding: 30px 20px;
            }

            .email-footer {
                padding: 25px 20px;
            }

            .email-header h1 {
                font-size: 24px;
            }

            .greeting {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header with GAP branding -->
        <div class="email-header">
            <div class="logo">
                GAP
            </div>
            <h1>{{ config('app.name', 'GAP 2025') }}</h1>
        </div>

        <!-- Email Body -->
        <div class="email-body">
            <div class="greeting">
                {{ $greeting ?? '¡Hola!' }}
            </div>

            <div class="content">
                {!! nl2br(e($body ?? 'Esta es una notificación de nuestro sistema.')) !!}
            </div>

            @if(isset($actionText) && isset($actionUrl))
            <div class="action-button">
                <a href="{{ $actionUrl }}" target="_blank" class="btn-primary">{{ $actionText }}</a>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-text">
                {{ $footerText ?? '¡Gracias por usar nuestra aplicación!' }}
            </div>

            <div class="app-info">
                <p>Este correo fue enviado desde {{ config('app.name', 'GAP 2025') }}</p>
                <p>Si tienes alguna pregunta, por favor contacta a nuestro equipo de soporte.</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'GAP 2025') }}. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
