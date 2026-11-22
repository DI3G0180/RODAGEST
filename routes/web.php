<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\AseoController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\QuejaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\QuejaPublicaController;
use App\Http\Controllers\HojaVidaController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

// Página raíz: siempre muestra el login para probar los 4 usuarios
Route::get('/', function () {
    return auth()->check()
        ? redirect()->to(route('dashboard', [], false))
        : view('auth.login');
});

// Formulario público de quejas por QR; el resto de RODAGEST permanece protegido.
Route::get('/reportar', [QuejaPublicaController::class, 'create'])->middleware('throttle:30,1')->name('quejas.publica');
Route::post('/reportar', [QuejaPublicaController::class, 'store'])->middleware('throttle:5,1')->name('quejas.publica.store');
Route::get('/seguimiento/{codigo}', [QuejaPublicaController::class, 'seguimiento'])->whereAlphaNumeric('codigo')->middleware('throttle:30,1')->name('quejas.seguimiento');
Route::get('/seguimiento/{codigo}/evidencia/{tipo}', [QuejaPublicaController::class, 'evidencia'])->whereAlphaNumeric('codigo')->whereIn('tipo', ['reporte', 'atencion'])->middleware('throttle:30,1')->name('quejas.seguimiento.evidencia');
Route::get('/2fa/verify', [TwoFactorController::class, 'challenge'])->middleware('throttle:10,1')->name('2fa.challenge');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->middleware('throttle:5,1')->name('2fa.verify');

// RUTAS PROTEGIDAS POR AUTENTICACIÓN
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard - Página principal (cada rol verá su contenido según el layout/views)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/quejas/qr-publico', [QuejaPublicaController::class, 'qr'])->middleware('role:admin')->name('quejas.qr-publico');
    Route::get('/reportes', [ReporteController::class, 'index'])->middleware('role:admin')->name('reportes.index');
    Route::get('/reportes/exportar', [ReporteController::class, 'export'])->middleware('role:admin')->name('reportes.export');
    Route::get('/unidades/{unidad}', [UnidadController::class, 'show'])
        ->whereNumber('unidad')->middleware('role:admin,inspector,operador,limpieza')->name('unidades.show');
    Route::get('/empleados', [EmpleadoController::class, 'index'])->middleware('role:admin')->name('empleados.index');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->middleware('role:admin')->name('empleados.store');
    Route::patch('/empleados/{empleado}', [EmpleadoController::class, 'update'])->middleware('role:admin')->name('empleados.update');
    Route::get('/empleados/{empleado}/hoja-de-vida', [HojaVidaController::class, 'showEmployee'])->middleware('role:admin')->name('empleados.hoja-vida');
    Route::patch('/empleados/{empleado}/hoja-de-vida', [HojaVidaController::class, 'update'])->middleware('role:admin')->name('empleados.hoja-vida.update');
    Route::get('/mi-hoja-de-vida', [HojaVidaController::class, 'own'])->middleware('role:operador')->name('empleados.hoja-vida.own');

    // 1. MÓDULO DE UNIDADES / ECO (gestión de flota solo Admin)
    Route::resource('unidades', UnidadController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->middleware('role:admin')->parameters([
        'unidades' => 'unidad'
    ]);

    // 2. MÓDULO DE INSPECCIONES (Admin e Inspector)
    Route::resource('inspecciones', InspeccionController::class)->only(['index', 'create', 'store', 'destroy'])->middleware('role:inspector')->parameters([
        'inspecciones' => 'inspeccion'
    ]);

    // Rutas adicionales para inspecciones
    Route::post('inspecciones/{inspeccion}/actualizar-estado', [InspeccionController::class, 'updateEstado'])->middleware('role:inspector')
        ->name('inspecciones.actualizar-estado');
    Route::post('inspecciones/{inspeccion}/cambiar-estado', [InspeccionController::class, 'cambiarEstado'])->middleware('role:inspector')
        ->name('inspecciones.cambiar-estado');

    // 3. MÓDULO DE QUEJAS (Admin, Inspector, Operador)
    Route::get('quejas', [QuejaController::class, 'index'])->middleware('role:admin,inspector,operador,limpieza')->name('quejas.index');
    Route::resource('quejas', QuejaController::class)->only(['destroy'])->middleware('role:admin,operador')->parameters([
        'quejas' => 'queja'
    ]);

    Route::put('quejas/{queja}/actualizar-estado', [QuejaController::class, 'updateEstado'])
        ->middleware('role:admin,inspector')->name('quejas.actualizar-estado');
    Route::post('quejas/{queja}/accion-limpieza', [QuejaController::class, 'accionLimpieza'])
        ->middleware('role:limpieza')->name('quejas.accion-limpieza');

    Route::post('/turnos', [TurnoController::class, 'store'])->middleware('role:operador')->name('turnos.store');
    Route::patch('/turnos/{turno}/cerrar', [TurnoController::class, 'close'])->middleware('role:operador')->name('turnos.close');

    // 4. MÓDULO DE ASEO / MANTENIMIENTO (Admin e Intendente)
    Route::resource('aseos', AseoController::class)->only(['index', 'create', 'store', 'destroy'])->middleware('role:limpieza')->parameters([
        'aseos' => 'aseo'
    ]);
    Route::patch('aseos/{aseo}', [AseoController::class, 'update'])->middleware('role:limpieza')->name('aseos.update');

    // Rutas adicionales para aseos
    Route::post('aseos/{aseo}/actualizar-estado', [AseoController::class, 'updateEstado'])->middleware('role:limpieza')
        ->name('aseos.actualizar-estado');

    // PERFIL DE USUARIO
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/two-factor', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::post('/profile/two-factor/enable', [TwoFactorController::class, 'enable'])->middleware('throttle:5,1')->name('2fa.enable');
    Route::delete('/profile/two-factor', [TwoFactorController::class, 'disable'])->middleware('throttle:5,1')->name('2fa.disable');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de autenticación (Laravel Breeze)
require __DIR__.'/auth.php';
