<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\CategoriasController;
use App\Http\Controllers\api\CursoController;
use App\Http\Controllers\api\NewsController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\EventoParticipanteController;
use Illuminate\Support\Facades\Route;


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

//mensaje de prueba para verificar que la API está funcionando
Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});

// Rutas para la gestión de noticias
Route::get('/news', [NewsController::class, 'index']); // Listar noticias
Route::get('/news/{id}', [NewsController::class, 'show']); // Obtener noticia por ID
Route::get('/news/category/{category}', [NewsController::class, 'getByCategory']); // Listar noticias por categoría
Route::get('/news/latest/{number?}', [NewsController::class, 'latest']); // Obtener últimas noticias

// Rutas para la gestión de categorías de noticias
Route::get('/categorias', [CategoriasController::class, 'index']); // Listar categorías de eventos


// rutas de cursos
Route::get('/curso', [CursoController::class, 'index']); // Listar de cursos
Route::get('/cursos/curso/{id?}', [CursoController::class, 'show']); // Obtener curso por ID
Route::get('/cursos/activos', [CursoController::class, 'activos']); // Obtener cursos activos
Route::get('/cursos/inactivos', [CursoController::class, 'inactivos']); // Obtener cursos inactivos
Route::get('/cursos/ordenados/fecha-inicio-desc', [CursoController::class, 'ordenadosPorFechaInicioDesc']); // Obtener cursos ordenados por fecha de inicio descendente
Route::get('/cursos/ultimos/{number?}', [CursoController::class, 'ultimosCursos']); // Obtener últimos cursos (por defecto 5)
