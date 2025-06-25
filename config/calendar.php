<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Calendar Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para optimización del calendario de eventos
    |
    */

    // Configuración de caché
    'cache' => [
        'enabled' => env('CALENDAR_CACHE_ENABLED', true),
        'ttl' => [
            'eventos' => env('CALENDAR_CACHE_TTL_EVENTOS', 300), // 5 minutos
            'usuarios' => env('CALENDAR_CACHE_TTL_USUARIOS', 1800), // 30 minutos
            'tipos_evento' => env('CALENDAR_CACHE_TTL_TIPOS', 3600), // 1 hora
        ],
    ],

    // Configuración de paginación AJAX
    'ajax' => [
        'enabled' => env('CALENDAR_AJAX_ENABLED', true),
        'batch_size' => env('CALENDAR_BATCH_SIZE', 50),
        'lazy_loading' => env('CALENDAR_LAZY_LOADING', true),
    ],

    // Configuración de consultas optimizadas
    'queries' => [
        'select_fields' => [
            'eventos' => ['id', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin', 'tipo_evento_id', 'creado_por', 'ubicacion', 'url_virtual', 'status'],
            'usuarios' => ['id', 'name', 'email'],
            'tipos_evento' => ['id', 'nombre', 'color'],
        ],
        'eager_loading' => [
            'eventos' => ['tipoEvento:id,nombre,color', 'participantes:id,name'],
            'usuarios' => ['roles:id,name'],
        ],
    ],

    // Configuración de JavaScript
    'javascript' => [
        'minified' => env('CALENDAR_JS_MINIFIED', true),
        'external_file' => env('CALENDAR_JS_EXTERNAL', true),
        'cdn_enabled' => env('CALENDAR_CDN_ENABLED', true),
    ],

    // Configuración de notificaciones
    'notifications' => [
        'enabled' => env('CALENDAR_NOTIFICATIONS_ENABLED', true),
        'type' => env('CALENDAR_NOTIFICATION_TYPE', 'toastr'), // toastr, sweetalert, native
    ],

    // Configuración de permisos
    'permissions' => [
        'recordatorios_personales' => [
            'crear' => ['alumno', 'profesor', 'administrador'],
            'editar' => ['creador'],
            'eliminar' => ['creador', 'administrador'],
            'ver' => ['creador', 'administrador'],
        ],
        'eventos_generales' => [
            'crear' => ['profesor', 'administrador'],
            'editar' => ['profesor', 'administrador'],
            'eliminar' => ['administrador'],
            'ver' => ['alumno', 'profesor', 'administrador'],
        ],
    ],
]; 