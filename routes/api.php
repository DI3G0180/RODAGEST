<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileOperationsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:10,1')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/unidades', [MobileOperationsController::class, 'units'])->middleware('role:admin,inspector,operador,limpieza');
        Route::post('/inspecciones', [MobileOperationsController::class, 'inspections'])->middleware('role:inspector');
        Route::get('/reportes', [MobileOperationsController::class, 'reports'])->middleware('role:operador');
        Route::post('/reportes', [MobileOperationsController::class, 'storeReport'])->middleware('role:operador');
        Route::get('/aseos', [MobileOperationsController::class, 'aseos'])->middleware('role:limpieza');
        Route::post('/aseos', [MobileOperationsController::class, 'storeAseo'])->middleware('role:limpieza');
    });
});
