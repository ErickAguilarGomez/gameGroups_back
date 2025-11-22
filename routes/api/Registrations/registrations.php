<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationReviewController;

Route::prefix('registrations')->group(function () {
    Route::get('/pending', [RegistrationReviewController::class, 'pending']);
    Route::post('/{userId}/approve', [RegistrationReviewController::class, 'approve']);
    Route::post('/{userId}/reject', [RegistrationReviewController::class, 'reject']);
    Route::get('/stats', [RegistrationReviewController::class, 'stats']);
});
