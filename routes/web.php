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
})->middleware(['auth', 'verified'])->name('dashboard');

// Ruta del dashboard de administrador
Route::get('/admin/dashboard', function () {
    return Inertia::render('AdminDashboard');
})->middleware(['auth', 'verified', 'role:Admin'])->name('admin.dashboard');

// Ruta principal de la aplicación
Route::get('/principal', function () {
    return Inertia::render('Principal');
})->middleware(['auth', 'verified'])->name('principal');

// Ruta para los usuarios
Route::get('/users', [UserController::class, 'index']);

// Rutas para los artículos
Route::post('/articulos/{articulo}/images', [ArticuloController::class, 'uploadImages']);
Route::post('/articulos', [ArticuloController::class, 'store']);

// Ruta para la vista de artículos
Route::get('/articulos', function () {
    return Inertia::render('ArticulosView');
})->name('articulos');

// Ruta para crear un nuevo artículo
Route::get('/articulos/create', function () {
    return Inertia::render('AddArticles');
})->middleware(['auth', 'verified'])->name('articulos.create');

Route::get('/addArticles', function () {
    return Inertia::render('addArticles');  
})->middleware(['auth', 'verified'])->name('addArticles');

Route::post('/articulos', [ArticuloController::class, 'store']);

// Ruta para almacenar artículos
Route::post('/articulos', [ArticuloController::class, 'store'])->name('articulos.store');

// Archivos de configuración y autenticación
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
