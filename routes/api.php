<?php
use App\Http\Controllers\EventoController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\EventoParticipanteController;
use App\Http\Controllers\Api\AuthController;
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
