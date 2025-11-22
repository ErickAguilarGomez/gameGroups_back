<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;

Route::prefix('groups')->group(function () {
    // Rutas específicas primero (antes de las rutas con parámetros)
    Route::get('/users/without-group', [GroupController::class, 'usersWithoutGroup']);
    Route::get('/users/banned', [GroupController::class, 'bannedUsers']);
    Route::post('/users/unban', [GroupController::class, 'unbanUser']);
    
    // Rutas de grupos
    Route::get('/', [GroupController::class, 'index']);
    Route::post('/', [GroupController::class, 'store']);
    Route::get('/{id}', [GroupController::class, 'show']);
    Route::put('/{id}', [GroupController::class, 'update']);
    Route::delete('/{id}', [GroupController::class, 'destroy']);
    Route::post('/{id}/assign', [GroupController::class, 'assignUser']);
    Route::post('/{id}/remove', [GroupController::class, 'removeUser']);
});
