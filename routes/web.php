<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\UserController;

Route::view('/', 'welcome');

Route::view('astro', 'template.base')
    ->name('astro');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard.index');
});

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
Route::prefix('admin/users')->name('admin.users.')->middleware(['auth'])->group(function () {
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

/* (Opcion 1)
    - Rutas hechas de manera manual
    - De momanto no estan protegidas 
    - De usar esta opcion usar middleware global o gestionar proteccion de otro lado.
*/

Route::get('admin/news', [NewsController::class, 'index'])->name('admin.news.index');
Route::get('admin/news/create', [NewsController::class, 'create'])->name('admin.news.create');
Route::post('admin/news', [NewsController::class, 'store'])->name('admin.news.store');
Route::get('admin/news/{news}', [NewsController::class, 'show'])->name('admin.news.show');
Route::get('admin/news/{news}/edit', [NewsController::class, 'edit'])->name('admin.news.edit');
Route::put('admin/news/{news}', [NewsController::class, 'update'])->name('admin.news.update');
Route::delete('admin/news/{news}', [NewsController::class, 'destroy'])->name('admin.news.destroy');

/* 
    (Opcion 2)
    - Bloque usando "Route::resource equivalente a todas las rutas CRUD estandar, con el prefijo admin en URL y name
    - Rutas protegidas con Middleware para que solo usuario identificado o admins tengan acceso
    - Necesario crear Middleware is_admin

    Route::prefix('admin')->name('admin.')->middleware(['auth', 'is_admin'])->group(function () {
    Route::resource('news', NewsController::class);
 });
*/
// 5179a36 (CRUD de noticias y categorias (pendiente de terminar las vistas))
