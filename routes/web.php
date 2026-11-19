<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\AseoController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\QuejaController;
use Illuminate\Support\Facades\Route;

// Página raíz: siempre muestra el login para probar los 4 usuarios
Route::get('/', function () {
    auth()->logout();
    return view('auth.login');
});

// RUTAS PROTEGIDAS POR AUTENTICACIÓN
Route::middleware(['auth'])->group(function () {

    // Dashboard - Página principal (cada rol verá su contenido según el layout/views)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 1. MÓDULO DE UNIDADES / ECO (Admin)
    Route::resource('unidades', UnidadController::class)->parameters([
        'unidades' => 'unidad'
    ]);

    // 2. MÓDULO DE INSPECCIONES (Admin e Inspector)
    Route::resource('inspecciones', InspeccionController::class)->parameters([
        'inspecciones' => 'inspeccion'
    ]);

    // Rutas adicionales para inspecciones
    Route::post('inspecciones/{inspeccion}/actualizar-estado', [InspeccionController::class, 'updateEstado'])
        ->name('inspecciones.actualizar-estado');
    Route::post('inspecciones/{inspeccion}/cambiar-estado', [InspeccionController::class, 'cambiarEstado'])
        ->name('inspecciones.cambiar-estado');

    // 3. MÓDULO DE QUEJAS (Admin, Inspector, Operador)
    Route::resource('quejas', QuejaController::class)->parameters([
        'quejas' => 'queja'
    ]);

    // Rutas adicionales para quejas
    Route::post('quejas/{queja}/upload-evidencia', [QuejaController::class, 'uploadEvidencia'])
        ->name('quejas.upload-evidencia');
    Route::put('quejas/{queja}/actualizar-estado', [QuejaController::class, 'updateEstado'])
        ->name('quejas.actualizar-estado');

    // 4. MÓDULO DE ASEO / MANTENIMIENTO (Admin e Intendente)
    Route::resource('aseos', AseoController::class)->parameters([
        'aseos' => 'aseo'
    ]);

    // Rutas adicionales para aseos
    Route::post('aseos/{aseo}/actualizar-estado', [AseoController::class, 'updateEstado'])
        ->name('aseos.actualizar-estado');

    // PERFIL DE USUARIO
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación (Laravel Breeze)
require __DIR__.'/auth.php';