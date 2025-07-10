<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriasController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\EventoApiController; // Agregado el nuevo controlador
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\EventoParticipanteController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\SolicitudCitaApiController;
use App\Http\Controllers\Api\AppointmentOptionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\EmailNotificationController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Rutas API para la app móvil y otros clientes externos
|--------------------------------------------------------------------------
*/

// Mensaje de prueba para verificar que la API está funcionando
Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});

// Rutas para la gestión de noticias
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']); // Listar noticias
    Route::get('/sliderNews', [NewsController::class, 'getSliderNews']); // Listar noticias
    Route::get('/{id}', [NewsController::class, 'getNoticiaById']); // Obtener noticia por id
});

// Rutas para la gestión de categorías de noticias
Route::get('/categorias', [CategoriasController::class, 'index']); // Listar categorías de eventos

// Endpoints de autenticación (públicos)
Route::post('auth/register', [AuthController::class, 'register']); // Registro de usuario móvil
Route::post('auth/login', [AuthController::class, 'login']);       // Login de usuario móvil

// Password Reset Routes (públicos)
Route::post('auth/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']); // Enviar email de recuperación (Laravel estándar)
Route::post('auth/password/reset', [ForgotPasswordController::class, 'reset']); // Resetear contraseña con token (Laravel estándar)

// Alternative Password Reset Routes for Mobile (públicos)
Route::post('auth/forgot-password', [AuthController::class, 'sendPasswordResetEmail']); // Enviar código de recuperación (móvil)
Route::post('auth/reset-password', [AuthController::class, 'resetPasswordWithToken']); // Resetear con código (móvil)

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']); // Logout y revocación de token
    Route::get('auth/me', [AuthController::class, 'me']);          // Info del usuario autenticado
    Route::post('auth/device', [AuthController::class, 'storeDevice']); // Guardar/actualizar info del dispositivo móvil
    Route::apiResource('eventos', EventoApiController::class)->names([
        'index' => 'api.eventos.index',
        'store' => 'api.eventos.store',
        'show' => 'api.eventos.show',
        'update' => 'api.eventos.update',
        'destroy' => 'api.eventos.destroy'
    ]); // Rutas para la API de eventos
    Route::apiResource('tipos-evento', TipoEventoController::class)->names([
        'index' => 'api.tipos-evento.index',
        'store' => 'api.tipos-evento.store',
        'show' => 'api.tipos-evento.show',
        'update' => 'api.tipos-evento.update',
        'destroy' => 'api.tipos-evento.destroy'
    ]);
    Route::apiResource('evento-participante', EventoParticipanteController::class)->names([
        'index' => 'api.evento-participante.index',
        'store' => 'api.evento-participante.store',
        'show' => 'api.evento-participante.show',
        'update' => 'api.evento-participante.update',
        'destroy' => 'api.evento-participante.destroy'
    ]);

    // Rutas específicas para agenda/calendario
    Route::get('agenda/eventos', [EventoApiController::class, 'agenda']); // Eventos para agenda/calendario
    Route::get('agenda/resumen', [EventoApiController::class, 'resumen']); // Resumen de eventos para dashboard

    // Rutas especializadas para frontend de agenda
    Route::prefix('agenda')->group(function () {
        Route::get('mes-actual', [AgendaController::class, 'mesActual']); // Eventos del mes para calendario
        Route::get('dia-actual', [AgendaController::class, 'diaActual']); // Eventos del día para agenda
        Route::get('estadisticas', [AgendaController::class, 'estadisticas']); // Estadísticas para dashboard
        Route::get('buscar', [AgendaController::class, 'buscar']); // Búsqueda de eventos
    });

    // Rutas para solicitudes de citas (académicas y médicas)
    Route::prefix('appointments')->group(function () {
        Route::get('/', [SolicitudCitaApiController::class, 'index']); // Listar solicitudes del usuario
        Route::post('/', [SolicitudCitaApiController::class, 'store']); // Crear nueva solicitud
        Route::get('/{id}', [SolicitudCitaApiController::class, 'show']); // Ver solicitud específica
        Route::patch('/{id}/status', [SolicitudCitaApiController::class, 'updateStatus']); // Actualizar estado (profesores)
        Route::delete('/{id}/cancel', [SolicitudCitaApiController::class, 'cancel']); // Cancelar solicitud (alumnos)
        Route::get('/statistics/summary', [SolicitudCitaApiController::class, 'statistics']); // Estadísticas
    });

    // Rutas para opciones de citas (profesores, especialidades, tratamientos)
    Route::prefix('appointment-options')->group(function () {
        Route::get('professors', [AppointmentOptionsController::class, 'professors']); // Lista de profesores
        Route::get('specialties', [AppointmentOptionsController::class, 'specialties']); // Especialidades médicas
        Route::get('treatments', [AppointmentOptionsController::class, 'treatments']); // Tratamientos médicos
        Route::get('doctors', [AppointmentOptionsController::class, 'doctors']); // Doctores por especialidad
        Route::get('all', [AppointmentOptionsController::class, 'all']); // Todas las opciones
        Route::get('available-slots', [AppointmentOptionsController::class, 'availableSlots']); // Horarios disponibles
    });

    // Rutas para chat entre usuarios (alumnos y profesores)
    Route::prefix('chat')->group(function () {
        Route::get('overview', [ChatApiController::class, 'getChatOverview']); // Obtener vista general del chat (chats recientes + usuarios disponibles)
        Route::get('users', [ChatApiController::class, 'getUsers']); // Obtener lista de usuarios para chatear
        Route::get('users/search', [ChatApiController::class, 'searchUsers']); // Buscar usuarios por rol y nombre
        Route::get('recent', [ChatApiController::class, 'getRecentChats']); // Obtener chats recientes
        Route::get('conversation/{userId}', [ChatApiController::class, 'getConversation']); // Obtener conversación con un usuario
        Route::post('send/{userId}', [ChatApiController::class, 'sendMessage']); // Enviar mensaje a un usuario
    });
}); // Close auth:sanctum middleware group

// Rutas para administración de usuarios pendientes (solo admin)
Route::middleware(['auth:sanctum', 'role:Administrador'])->group(function () {
    Route::get('admin/users/pending', [AuthController::class, 'pendingUsers']); // Listar usuarios pendientes
    Route::post('admin/users/{user}/validate', [AuthController::class, 'validateAndAssignRole']); // Validar y asignar rol
});

// Rutas para cursos autenticadas (requieren token Sanctum)
Route::middleware('auth:sanctum')->prefix('cursos')->group(function () {
    Route::get('/mis-cursos', [CursoController::class, 'misCursos']); // Mis cursos
    Route::post('/{id}/suscribirse', [CursoController::class, 'suscribirse']); // Suscribirse a curso
    Route::delete('/{id}/cancelar-suscripcion', [CursoController::class, 'cancelarSuscripcion']); // Cancelar suscripción
});

// Rutas para cursos (públicas - no requieren autenticación)
Route::prefix('cursos')->group(function () {
    Route::get('/', [CursoController::class, 'index']); // Listar todos los cursos
    Route::get('/activos', [CursoController::class, 'activos']); // Cursos activos
    Route::get('/inactivos', [CursoController::class, 'inactivos']); // Cursos inactivos
    Route::get('/ordenados/fecha-inicio-desc', [CursoController::class, 'ordenadosPorFechaInicioDesc']); // Ordenados por fecha
    Route::get('/ultimos/{n?}', [CursoController::class, 'ultimosCursos']); // Últimos N cursos
    // IMPORTANT: Wildcard routes must come LAST
    Route::get('/{id}', [CursoController::class, 'show']); // Mostrar curso específico
});

// Registro de dispositivo SIN autenticación (primer contacto, sin usuario)
Route::post('auth/device/register', [AuthController::class, 'registerDevice']);

// Guardar/actualizar token FCM para usuario autenticado
Route::middleware('auth:sanctum')->post('/fcm-token', [NotificationController::class, 'store']);

// Endpoints de dispositivos (usuarios autenticados)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/devices', [DeviceController::class, 'store']);
    Route::delete('/devices', [DeviceController::class, 'destroy']);
});

// Enviar notificación a usuarios seleccionados - solo admin
Route::middleware(['auth:sanctum', 'admin'])->post('/notifications/send', [NotificationController::class, 'sendNotification']);
// Nueva ruta para FCM HTTP v1
Route::middleware(['auth:sanctum', 'admin'])->post('/notifications/send-fcm-v1', [NotificationController::class, 'sendFcmV1']);
// Ruta para enviar notificaciones WebPush
Route::middleware(['auth:sanctum', 'admin'])->post('/notifications/send-webpush', [App\Http\Controllers\Api\NotificationController::class, 'sendWebPush']);

// Email notification routes (admin only)
Route::middleware(['auth:sanctum', 'admin'])->prefix('email-notifications')->group(function () {
    Route::post('/send', [EmailNotificationController::class, 'sendNotification'])
        ->name('api.email-notifications.send');
    Route::post('/send-to-user/{userId}', [EmailNotificationController::class, 'sendToUser'])
        ->name('api.email-notifications.send-to-user');
});

// Debug route to test Sanctum authentication
Route::middleware('auth:sanctum')->get('debug/auth-test', function(Request $request) {
    return response()->json([
        'authenticated' => true,
        'user_id' => $request->user()->id,
        'user_email' => $request->user()->email,
        'token_id' => $request->user()->currentAccessToken()->id ?? null
    ]);
});