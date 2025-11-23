<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoReview\PhotoReviewController;

Route::prefix('photos')->group(function () {
    Route::get('/pending', [PhotoReviewController::class, 'pending']);
    Route::post('/{userId}/approve', [PhotoReviewController::class, 'approve']);
    Route::post('/{userId}/reject', [PhotoReviewController::class, 'reject']);
    Route::get('/stats', [PhotoReviewController::class, 'stats']);
});
