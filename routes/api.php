<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriasController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\EventoParticipanteController;
use App\Http\Controllers\Api\CursoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DeviceController;
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

// Endpoints de recuperación de contraseña para la app móvil
Route::post('auth/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('auth/reset-password', [ForgotPasswordController::class, 'reset']);

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']); // Logout y revocación de token
    Route::get('auth/me', [AuthController::class, 'me']);          // Info del usuario autenticado
    Route::post('auth/device', [AuthController::class, 'storeDevice']); // Guardar/actualizar info del dispositivo móvil
    Route::apiResource('eventos', EventoController::class);
    Route::apiResource('tipos-evento', TipoEventoController::class);
    Route::apiResource('evento-participante', EventoParticipanteController::class);
});

// Rutas para administración de usuarios pendientes (solo admin)
Route::middleware(['auth:sanctum', 'role:Administrador'])->group(function () {
    Route::get('admin/users/pending', [AuthController::class, 'pendingUsers']); // Listar usuarios pendientes
    Route::post('admin/users/{user}/validate', [AuthController::class, 'validateAndAssignRole']); // Validar y asignar rol
});

// Rutas para cursos

Route::prefix('cursos')->group(function () {
    Route::get('/', [CursoController::class, 'index']);
    Route::get('/{id}', [CursoController::class, 'show']);
    Route::get('/activos', [CursoController::class, 'activos']);
    Route::get('/inactivos', [CursoController::class, 'inactivos']);
    Route::get('/ordenados/fecha-inicio-desc', [CursoController::class, 'ordenadosPorFechaInicioDesc']);
    Route::get('/ultimos/{n?}', [CursoController::class, 'ultimosCursos']);
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

// Rutas para citas con IA
Route::middleware('auth:sanctum')->prefix('appointments')->group(function () {
    Route::post('/suggest', [App\Http\Controllers\AiAppointmentController::class, 'suggest']);
    Route::post('/', [App\Http\Controllers\AiAppointmentController::class, 'create']);
});

// Ruta para obtener eventos para el calendario del dashboard
Route::get('/events', function () {
    $eventos = \App\Models\Evento::select('id', 'titulo', 'fecha_inicio', 'fecha_fin')
        ->orderBy('fecha_inicio', 'asc')
        ->get()
        ->map(function ($evento) {
            return [
                'id' => $evento->id,
                'titulo' => $evento->titulo,
                'fecha' => $evento->fecha_inicio->format('Y-m-d'),
                'hora_inicio' => $evento->fecha_inicio->format('H:i'),
                'hora_fin' => $evento->fecha_fin->format('H:i')
            ];
        });
    
    return response()->json($eventos);
});