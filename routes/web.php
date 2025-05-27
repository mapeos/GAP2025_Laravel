<?php

use Illuminate\Support\Facades\Route;

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
