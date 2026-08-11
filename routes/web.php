<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\AseoController;
use App\Http\Controllers\InspeccionController;
use Illuminate\Support\Facades\Route;

// Redirigir la raíz al inicio de sesión o al dashboard de inspecciones
Route::get('/', function () {
    return redirect()->route('inspecciones.index');
});

// Rutas protegidas por Autenticación
Route::middleware(['auth', 'verified'])->group(function () {

    // Redirección opcional de /dashboard a la vista principal del sistema
    Route::get('/dashboard', function () {
        return redirect()->route('inspecciones.index');
    })->name('dashboard');

    // Módulos principales de RodaGest
    Route::resource('unidades', UnidadController::class);
    Route::resource('aseos', AseoController::class);
    Route::resource('inspecciones', InspeccionController::class);
    Route::get('/unidades', [UnidadController::class, 'index'])->name('unidades.index');
    Route::delete('/unidades/{id}', [UnidadController::class, 'destroy'])->name('unidades.destroy');
    // Nota: Para editar, podrías usar un modal o una vista separada.
    Route::get('/unidades/create', [UnidadController::class, 'create'])->name('unidades.create');
    Route::post('/unidades', [UnidadController::class, 'store'])->name('unidades.store');

    // Perfil de Usuario (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
