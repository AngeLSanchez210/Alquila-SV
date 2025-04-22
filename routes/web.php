<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\PuntuacionController;
use App\Http\Controllers\CategoriaController;
use App\Http\Middleware\Denegade;


// Ruta de inicio
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Ruta del dashboard del usuario
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified' ])->name('dashboard');

// Ruta del dashboard de administrador
Route::get('/admin/dashboard', function () {
    return Inertia::render('AdminDashboard');
})->middleware(['auth', 'verified', 'role:Admin'])->name('admin.dashboard');


// ADMIN ELIMINAR E EDITAR USUARIOS
// Ruta para crear un nuevo usuario
Route::post('/users', [UserController::class, 'store'])->name('users.store');

// Mostrar listado de usuarios
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Ruta para editar un usuario
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

// Ruta para actualizar un usuario
Route::put('/users/{user}', [UserController::class, 'update']);

// Ruta para eliminar un usuario
Route::delete('/users/{user}', [UserController::class, 'destroy']);

// Ruta para mostrar los datos de un usuario específico
Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');

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
Route::get('/articulos/create', [ArticuloController::class, 'create'])->middleware(['auth', 'verified'])->name('articulos.create');


// Ruta para almacenar artículos
Route::post('/articulos', [ArticuloController::class, 'store'])->name('articulos.store');



//ADMIN PANEL
Route::get('/admin/users', function () {
    return Inertia::render('admin/adminUsers'); 
})->name('admin.users');

Route::get('/admin/items', function () {
    return Inertia::render('admin/adminArticles'); 
})->name('admin.items');


Route::get('/articulos/search', [ArticuloController::class, 'apiSearch'])->name('api.articulos.search');






Route::middleware([Denegade::class, 'verified'])->group(function () {
    
    Route::get('/api/favoritos', [FavoritoController::class, 'index'])->name('api.favoritos');
    Route::post('/api/favoritos', [FavoritoController::class, 'store'])->name('favoritos.store');
    Route::delete('/favoritos/{favorito}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/api/user/articulos', [ArticuloController::class, 'getUserArticulos']);
    Route::put('/api/articulos/{articulo}', [ArticuloController::class, 'update']);
    Route::delete('/api/articulos/{articulo}', [ArticuloController::class, 'destroy']);
    Route::delete('/api/articulos/imagenes/{imagen}', [ArticuloController::class, 'eliminarImagen']);
    Route::post('/api/articulos/{articulo}/imagenes', [ArticuloController::class, 'subirImagenes']);
    Route::get('/api/favoritos', [FavoritoController::class, 'index'])->name('api.favoritos');
    Route::delete('/favoritos/{favorito}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');
});

Route::post('/puntuaciones', [PuntuacionController::class, 'store'])->name('puntuaciones.store');
Route::get('/puntuaciones/{articuloId}/{userId}', [PuntuacionController::class, 'verificarPuntuacion']);


// Ruta para ver el detalle de un solo artículo
Route::get('/articulos/ver/{articulo}', [ArticuloController::class, 'ver'])->name('articulos.ver');


Route::get('/categorias', [CategoriaController::class, 'index']);

// Archivos de configuración y autenticación
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
