<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;

Route::prefix('groups')->group(function () {
    Route::post('/users/unban', [GroupController::class, 'unbanUser']);
    Route::get('/all-groups', [GroupController::class, 'groups']);
    Route::get('/user-detail/{id}', [GroupController::class, 'userDetail']);
    Route::post('/', [GroupController::class, 'store']);
    Route::put('/{id}', [GroupController::class, 'update']);
    Route::delete('/{id}', [GroupController::class, 'destroy']);
    Route::post('/{id}/assign', [GroupController::class, 'assignUser']);
    Route::post('/{id}/remove', [GroupController::class, 'removeUser']);
});
