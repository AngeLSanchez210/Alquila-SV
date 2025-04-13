<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticuloController;

// Ruta de inicio
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Ruta del dashboard del usuario
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:Admin'])->name('dashboard');

// Ruta del dashboard de administrador
Route::get('/admin/dashboard', function () {
    return Inertia::render('AdminDashboard');
})->middleware(['auth', 'verified', 'role:Admin'])->name('admin.dashboard');

// ADMIN ELIMINAR E EDITAR USUARIOS
// Mostrar listado de usuarios
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Ruta para editar un usuario
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

// Ruta para actualizar un usuario
Route::put('/users/{user}', [UserController::class, 'update']);

// Ruta para eliminar un usuario
Route::delete('/users/{user}', [UserController::class, 'destroy']);

// Rutas para los artículos
Route::post('/articulos/{articulo}/images', [ArticuloController::class, 'uploadImages']);
Route::post('/articulos', [ArticuloController::class, 'store']);

// Ruta para la vista de artículos
Route::get('/articulos', function () {
    return Inertia::render('ArticulosView');
})->name('articulos');

// Ruta para obtener el JSON de los artículos
Route::get('/api/articulos', [ArticuloController::class, 'index'])->name('api.articulos');

// Ruta para crear un nuevo artículo
Route::get('/articulos/create', function () {
    return Inertia::render('AddArticles');
})->middleware(['auth', 'verified'])->name('articulos.create');

// Ruta para almacenar artículos
Route::post('/articulos', [ArticuloController::class, 'store'])->name('articulos.store');

// Archivos de configuración y autenticación
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
