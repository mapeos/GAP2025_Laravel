<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la generación de códigos QR en diplomas
    |
    */

    // Tamaño del QR por defecto
    'size' => 200,

    // Formato por defecto (svg, png, eps)
    'format' => 'svg',

    // Estilo del QR
    'style' => 'square',

    // Estilo de los ojos del QR
    'eye' => 'square',

    // Margen del QR
    'margin' => 1,

    // Corrección de errores (L, M, Q, H)
    'error_correction' => 'M',

    // URL base para los cursos
    'base_url' => env('APP_URL', 'http://localhost'),

    // Directorio para guardar QR codes
    'storage_path' => 'qrcodes/cursos',

    // Configuración para diplomas
    'diploma' => [
        'qr_size' => 120,
        'qr_format' => 'svg',
        'qr_style' => 'square',
        'qr_eye' => 'square',
        'qr_margin' => 1,
        'qr_error_correction' => 'M',
    ],
]; 