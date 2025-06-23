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
Route::middleware('auth:sanctum')->prefix('cursos')->name('api.cursos.')->group(function () {
    Route::post('/', [CursoController::class, 'store'])->name('store'); // Crear un curso
    Route::get('/', [CursoController::class, 'index'])->name('index'); // Listar cursos
    Route::get('/create', [CursoController::class, 'create'])->name('create'); // Formulario de creación de curso
    Route::get('/{curso}/edit', [CursoController::class, 'edit'])->name('edit'); // Formulario de edición de curso
    Route::delete('/{curso}', [CursoController::class, 'destroy'])->name('destroy'); // Eliminar un curso
    Route::get('/activos', [CursoController::class, 'activos']); // Cursos activos
    Route::get('/inactivos', [CursoController::class, 'inactivos']); // Cursos inactivos
    Route::get('/ordenados/fecha-inicio-desc', [CursoController::class, 'ordenadosPorFechaInicioDesc']); // Cursos ordenados por fecha
    Route::get('/ultimos/{n?}', [CursoController::class, 'ultimosCursos']); // Últimos cursos
    Route::get('/{id}', [CursoController::class, 'show'])->name('show'); // Ver detalles de un curso
    Route::put('/{curso}', [CursoController::class, 'update'])->name('update'); // Actualizar un curso
    Route::get('/buscar', [CursoController::class, 'buscarFiltrar']); // Buscar cursos: /api/cursos/buscar?search=foo&estado=activo&orden=desc
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