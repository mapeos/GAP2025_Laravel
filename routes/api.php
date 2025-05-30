<?php
use App\Http\Controllers\EventoController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\EventoParticipanteController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('eventos', EventoController::class);
    Route::apiResource('tipos-evento', TipoEventoController::class);
    Route::apiResource('evento-participante', EventoParticipanteController::class);
});
