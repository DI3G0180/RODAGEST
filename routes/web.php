<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\AseoController;
use App\Http\Controllers\InspeccionController;
use Illuminate\Support\Facades\Route;

// Redirigir la raíz según el rol del usuario autenticado
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'operador' 
            ? redirect()->route('aseos.index') 
            : redirect()->route('inspecciones.index');
    }
    return redirect()->route('login');
});

// RUTAS PROTEGIDAS POR AUTENTICACIÓN
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        // Si el usuario es operador, redirigir a Aseos; si no, a Inspecciones
        if (auth()->user()->role === 'operador') {
            return redirect()->route('aseos.index');
        }
        return redirect()->route('inspecciones.index');
    })->name('dashboard');

    // 1. ACCESO GENERAL (Admin, Inspector y Operador)
    Route::resource('aseos', AseoController::class);

    // 2. ACCESO SOLO INSPECTORES Y ADMINS
    Route::middleware(['can:isInspector'])->group(function () {
        Route::resource('inspecciones', InspeccionController::class);
        Route::resource('unidades', UnidadController::class);
    });

    // 3. ACCESO EXCLUSIVO ADMINISTRADOR
    Route::middleware(['can:isAdmin'])->group(function () {
        // Reservado para futuras rutas administrativas
    });

    // PERFIL DE USUARIO
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';