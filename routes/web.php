<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TipoEventoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\EventoParticipanteController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::view('astro', 'template.base')
    ->name('astro');

Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'role:Administrador'])
    ->name('admin.dashboard');

Route::get('/admin/pagina-test', function () {
    return view('admin.dashboard.test');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

// Rutas para el módulo de Agenda/Calendario
Route::middleware(['auth'])->group(function () {
    // Rutas para tipos de evento (solo administradores)
    Route::middleware(['role:Administrador'])->group(function () {
        Route::resource('tipos-evento', TipoEventoController::class);
    });
    
    // Rutas para eventos
    Route::get('calendario', [EventoController::class, 'calendario'])->name('calendario');
    Route::get('eventos/json', [EventoController::class, 'getEventos'])->name('eventos.json');
    
    // Rutas CRUD de eventos (solo administradores y profesores)
    Route::middleware(['role:Administrador|Profesor'])->group(function () {
        Route::resource('eventos', EventoController::class);
    });
    
    // Rutas para participantes
    Route::prefix('eventos/{evento}/participantes')->group(function () {
        Route::post('asistencia', [EventoParticipanteController::class, 'updateAsistencia'])->name('eventos.participantes.asistencia');
        Route::post('add', [EventoParticipanteController::class, 'addParticipantes'])->name('eventos.participantes.add');
        Route::post('remove', [EventoParticipanteController::class, 'removeParticipantes'])->name('eventos.participantes.remove');
        Route::post('rol', [EventoParticipanteController::class, 'updateRol'])->name('eventos.participantes.rol');
    });
});

// Rutas de Cursos Públicas
Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
Route::get('/cursos/create', [CursoController::class, 'create'])->name('cursos.create');
Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');

// Rutas protegidas para Cursos
Route::middleware(['auth'])->group(function () {
    Route::get('/cursos/{id}', [CursoController::class, 'show'])->name('cursos.show');
    Route::get('/cursos/{curso}/edit', [CursoController::class, 'edit'])->name('cursos.edit');
    Route::put('/cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');
});

// Rutas administración de usuarios con prefijo y nombre de ruta
Route::prefix('admin/users')->name('admin.users.')->middleware(['auth', 'role:Administrador'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');           // Lista de usuarios
    Route::get('/create', [UserController::class, 'create'])->name('create');   // Formulario de creación
    Route::post('/', [UserController::class, 'store'])->name('store');          // Guardar nuevo usuario
    Route::get('/{user}', [UserController::class, 'show'])->name('show');       // Ver detalle (opcional)
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');  // Formulario de edición
    Route::put('/{user}', [UserController::class, 'update'])->name('update');   // Actualizar usuario
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy'); // Eliminar usuario
    Route::post('/{id}/restore', [UserController::class, 'restore'])->name('restore'); // Restaurar usuario eliminado
});

require __DIR__ . '/auth.php';

//-------------------------------------------
// Rutas de News - CRUD

Route::get('admin/news', [NewsController::class, 'index'])->name('admin.news.index');
Route::get('admin/news/create', [NewsController::class, 'create'])->name('admin.news.create');
Route::post('admin/news', [NewsController::class, 'store'])->name('admin.news.store');
Route::get('admin/news/{news}', [NewsController::class, 'show'])->name('admin.news.show');
Route::get('admin/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
Route::put('admin/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
Route::delete('admin/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');

//-------------------------------------------
// Rutas de Categorias - CRUD

Route::get('admin/categorias', [CategoriasController::class, 'index'])->name('admin.categorias.index');
Route::get('admin/categorias/create', [CategoriasController::class, 'create'])->name('admin.categorias.create');
Route::post('admin/categorias', [CategoriasController::class, 'store'])->name('admin.categorias.store');
// Route::get('admin/categorias/{categoria}', [CategoriasController::class, 'show'])->name('admin.categorias.show'); opcional si quieres mostrar una categoría
Route::get('admin/categorias/{categoria}/edit', [CategoriasController::class, 'edit'])->name('admin.categorias.edit');
Route::put('admin/categorias/{categoria}', [CategoriasController::class, 'update'])->name('admin.categorias.update');
Route::delete('admin/categorias/{categoria}', [CategoriasController::class, 'destroy'])->name('admin.categorias.destroy');

//--------------------------------------------
// Rutas para el rol Alumno
Route::middleware(['auth', 'role:Alumno'])->group(function () {
    Route::view('/alumno/home', 'alumno.home')->name('alumno.home');
});

// Rutas para el rol Profesor
Route::middleware(['auth', 'role:Profesor'])->group(function () {
    Route::view('/profesor/home', 'profesor.home')->name('profesor.home');
});

//-------------------------------------------
// Rutas de Events - CRUD
Route::prefix('admin/events')->name('admin.events.')->middleware(['auth', 'role:Administrador|Profesor'])->group(function () {
    // Rutas para tipos de evento (solo administradores)
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/types', [TipoEventoController::class, 'index'])->name('types.index');
        Route::get('/types/create', [TipoEventoController::class, 'create'])->name('types.create');
        Route::post('/types', [TipoEventoController::class, 'store'])->name('types.store');
        Route::get('/types/{tipoEvento}/edit', [TipoEventoController::class, 'edit'])->name('types.edit');
        Route::put('/types/{tipoEvento}', [TipoEventoController::class, 'update'])->name('types.update');
        Route::delete('/types/{tipoEvento}', [TipoEventoController::class, 'destroy'])->name('types.destroy');
    });

    // Rutas CRUD de eventos
    Route::get('/', [EventoController::class, 'index'])->name('index');
    Route::get('/create', [EventoController::class, 'create'])->name('create');
    Route::post('/', [EventoController::class, 'store'])->name('store');
    Route::get('/{evento}', [EventoController::class, 'show'])->name('show');
    Route::get('/{evento}/edit', [EventoController::class, 'edit'])->name('edit');
    Route::put('/{evento}', [EventoController::class, 'update'])->name('update');
    Route::delete('/{evento}', [EventoController::class, 'destroy'])->name('destroy');

    // Rutas para participantes
    Route::prefix('{evento}/participants')->name('participants.')->group(function () {
        Route::post('/attendance', [EventoParticipanteController::class, 'updateAsistencia'])->name('attendance');
        Route::post('/add', [EventoParticipanteController::class, 'addParticipantes'])->name('add');
        Route::post('/remove', [EventoParticipanteController::class, 'removeParticipantes'])->name('remove');
        Route::post('/role', [EventoParticipanteController::class, 'updateRol'])->name('role');
    });
});

// Rutas para todos los usuarios autenticados
Route::prefix('events')->name('events.')->middleware(['auth'])->group(function () {
    Route::get('/calendar', [EventoController::class, 'calendario'])->name('calendar');
    Route::get('/json', [EventoController::class, 'getEventos'])->name('json');
    Route::get('/{evento}', [EventoController::class, 'show'])->name('show');
});


