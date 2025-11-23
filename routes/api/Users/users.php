<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

Route::prefix('users')->group(function () {
    // Rutas de usuario autenticado
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/photo', [UserController::class, 'updatePhoto']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // Rutas de gestión de usuarios (CEO)
    Route::get('/', [UserController::class, 'index']);
    Route::post('/by-tab', [UserController::class, 'getUsersByTab']);
    Route::get('/counters', [UserController::class, 'getCounters']);
    Route::post('/approve-photo', [UserController::class, 'approvePhoto']);
    Route::post('/reject-photo', [UserController::class, 'rejectPhoto']);
    Route::post('/approve-account', [UserController::class, 'approveAccount']);
    Route::post('/reject-account', [UserController::class, 'rejectAccount']);
    Route::post('/approve-with-photo', [UserController::class, 'approveWithPhoto']);
    Route::post('/approve-without-photo', [UserController::class, 'approveWithoutPhoto']);
    Route::post('/update', [UserController::class, 'updateViaPost']);
    Route::post('/delete', [UserController::class, 'destroyViaPost']);

    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});
