<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;

Route::prefix('tokens')->group(function () {
    Route::post('/revoke', [TokenController::class, 'revokeToken']);
    Route::post('/revoke-all', [TokenController::class, 'revokeAllTokens']);
    Route::get('/', [TokenController::class, 'listTokens']);
});

// Public token creation (outside auth)
Route::post('/create', [TokenController::class, 'createToken']);
