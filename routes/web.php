<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\PuntuacionController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PlanController;
use App\Http\Middleware\Denegade;
use App\Models\Plan;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\SeguidorController;
use App\Http\Controllers\SuscripcionController;

Route::get('/api/seguidores/{seguidor_id}/{seguido_id}', [SeguidorController::class, 'checkFollowing']);

// Ruta de inicio
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:Admin'])->name('dashboard');


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
// Route::post('/articulos', [ArticuloController::class, 'store']);

// Ruta para la vista de artículos
Route::get('/articulos', function () {
    return Inertia::render('ArticulosView');
})->name('articulos');

// Ruta para obtener el JSON de los artículos
Route::get('/api/articulos', [ArticuloController::class, 'index'])->name('api.articulos');

// Ruta para crear un nuevo artículo
Route::get('/articulos/create', [ArticuloController::class, 'create'])->middleware(['auth', 'verified'])->name('articulos.create');


// Ruta para almacenar artículos
Route::post('/articulos', [ArticuloController::class, 'store'])->middleware(['auth', 'verified'])->name('articulos.store');




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
    Route::get('/pay', function () {
        return Inertia::render('Pay');
    })->name('pay');
});

// Ruta para almacenar un pago (solo para usuarios autenticados)
Route::middleware(['auth'])->group(function () {
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
});

Route::post('/puntuaciones', [PuntuacionController::class, 'store'])->name('puntuaciones.store');
Route::get('/puntuaciones/{articuloId}/{userId}', [PuntuacionController::class, 'verificarPuntuacion']);


// Ruta para ver el detalle de un solo artículo
Route::get('/articulos/ver/{articulo}', [ArticuloController::class, 'ver'])->name('articulos.ver');


Route::get('/categorias', [CategoriaController::class, 'index']);

// Ruta para la vista de los planes
Route::get('/planes', function () {
    return inertia::render('admin/Planes');
})->name('planes');

// Ruta para crear un nuevo plan
Route::post('/planes', [PlanController::class, 'store'])->name('planes.store');

// Ruta para actualizar un plan
Route::put('/planes/{plan}', [PlanController::class, 'update'])->name('planes.update');

// Ruta para eliminar un plan
Route::delete('/planes/{plan}', [PlanController::class, 'destroy'])->name('planes.destroy');

// Ruta para obtener los planes en formato JSON
Route::get('/api/planes', [PlanController::class, 'index'])->name('api.planes');

Route::get('/api/planes/{plan}', [PlanController::class, 'show']);

Route::get('/api/articulos/{articulo}', [ArticuloController::class, 'show'])->name('api.articulos.show');


// Ruta para la vista de perfil de usuario
Route::get('/profile/{user_id}', function ($user_id) {
    $user = \App\Models\User::find($user_id);

    if (!$user) {
        return Inertia::render('Errors/404');
    }

    $articulosCount = \App\Models\Articulo::where('usuario_id', $user_id)->count();
    $seguidoresCount = \App\Models\Seguidor::where('seguido_id', $user_id)->count();
    $isPremium = \App\Models\Suscripcion::where('usuario_id', $user_id)->where('plan_id', '>', 1)->exists();

    return Inertia::render('SeguirCompleto', [
        'userId' => $user_id,
        'userName' => $user->name,
        'userEmail' => $user->email,
        'articulosCount' => $articulosCount,
        'seguidoresCount' => $seguidoresCount,
        'isPremium' => $isPremium
    ]);
})->name('profile');

// Ruta de fallback para manejar rutas no encontradas
Route::fallback(function () {
    return Inertia::render('Errors/404');
});

// Archivos de configuración y autenticación
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Rutas para la funcionalidad de seguidores
Route::middleware(['auth'])->group(function () {
    Route::post('/seguir', [SeguidorController::class, 'store'])->name('seguir');
    Route::post('/dejar-seguir', [SeguidorController::class, 'destroy']);
});

// Rutas para manejar la imagen del usuario
Route::get('/api/users/{user}/image', [UserController::class, 'getImage'])->name('users.image.get');
Route::post('/api/users/{user}/image', [UserController::class, 'uploadImage'])->name('users.image.upload');
Route::get('/api/users/{user}/suscripcion-activa', [SuscripcionController::class, 'getSuscripcionActiva']);


