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

//mensaje de prueba para verificar que la API está funcionando
Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});

// Rutas para la gestión de noticias
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']); // Listar noticias
    Route::get('/latest', [NewsController::class, 'latest']);  // Obtener últimas noticias
    Route::get('/{id}', [NewsController::class, 'show']); // Obtener noticia por ID
    Route::post('/', [NewsController::class, 'store']);
    Route::get('/category/{category}', [NewsController::class, 'getByCategory']); // Listar noticias por categoría

});

// Rutas para la gestión de categorías de noticias
Route::get('/categorias', [CategoriasController::class, 'index']); // Listar categorías de eventos


/*
|--------------------------------------------------------------------------
| Rutas API para la app móvil y otros clientes externos
|--------------------------------------------------------------------------
|
| - Los endpoints de autenticación (register, login) son públicos.
| - El resto de rutas están protegidas por Sanctum (token en header Authorization).
| - Los controladores gestionan eventos, tipos de evento y participantes.
| - La ruta auth/device permite registrar o actualizar la info del dispositivo móvil.
|
| Si tienes dudas sobre el flujo, revisa el README.md (apartado de autenticación móvil).
|
*/

Route::post('auth/register', [AuthController::class, 'register']); // Registro de usuario móvil
Route::post('auth/login', [AuthController::class, 'login']);       // Login de usuario móvil

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

///Rutas para cursos

Route::middleware('auth:sanctum')->prefix('cursos')->name('api.cursos.')->group(function () {
    Route::post('/', [CursoController::class, 'store'])->name('store'); // Crear un curso
    Route::get('/', [CursoController::class, 'index'])->name('index'); // Listar cursos
    Route::get('/create', [CursoController::class, 'create'])->name('create'); // Formulario de creación de curso
    Route::get('/{curso}/edit', [CursoController::class, 'edit'])->name('edit'); // Formulario de edición de curso
    Route::delete('/{curso}', [CursoController::class, 'destroy'])->name('destroy'); // Eliminar un curso
    Route::get('/activos', [CursoController::class, 'activos']); // Cursos activos: GET /api/cursos/activos
    Route::get('/inactivos', [CursoController::class, 'inactivos']); // Cursos inactivos: GET /api/cursos/inactivos
    Route::get('/ordenados/fecha-inicio-desc', [CursoController::class, 'ordenadosPorFechaInicioDesc']); // Cursos ordenados por fecha
    Route::get('/ultimos/{n?}', [CursoController::class, 'ultimosCursos']); // Últimos cursos: GET /api/cursos/ultimos/5
    
    Route::get('/{id}', [CursoController::class, 'show'])->name('show'); // Ver detalles de un curso
    Route::put('/{curso}', [CursoController::class, 'update'])->name('update'); // Actualizar un curso
    Route::get('/cursos/buscar', [CursoController::class, 'buscarFiltrar']); // GET /api/cursos/buscar?search=foo&estado=activo&orden=desc

});


// Save/update FCM token for authenticated user
Route::middleware('auth:sanctum')->post('/fcm-token', [NotificationController::class, 'store']);
// Devices endpoints for storing/deleting device info (authenticated users)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/devices', [DeviceController::class, 'store']);
    Route::delete('/devices', [DeviceController::class, 'destroy']);
});
// Send notification to selected users - only admin (you may want to use sanctum guard here for consistency)
Route::middleware(['auth:sanctum', 'admin'])->post('/notifications/send', [NotificationController::class, 'sendNotification']);
// Nueva ruta para FCM HTTP v1
Route::middleware(['auth:sanctum', 'admin'])->post('/notifications/send-fcm-v1', [NotificationController::class, 'sendFcmV1']);