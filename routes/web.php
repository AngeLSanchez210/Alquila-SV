<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticuloController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified' ,'role:Admin' ])->name('dashboard');

Route::get('/admin/dashboard', function () {
    return Inertia::render('AdminDashboard');
})->middleware(['auth', 'verified', 'role:Admin'])->name('admin.dashboard');

Route::get('/principal', function () {
    return Inertia::render('Principal');
})->middleware(['auth', 'verified'])->name('principal');

Route::get('/users', [UserController::class, 'index']);

Route::post('/articulos/{articulo}/images', [ArticuloController::class, 'uploadImages']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
