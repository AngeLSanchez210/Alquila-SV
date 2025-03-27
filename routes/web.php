<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/admin/dashboard', function () {
    return Inertia::render('AdminDashboard');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::get('/principal', function () {
    return Inertia::render('Principal');
})->middleware(['auth', 'verified'])->name('principal');




Route::get('/users', [UserController::class, 'index']);


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
