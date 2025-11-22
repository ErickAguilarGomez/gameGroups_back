<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Announcement\AnnouncementController;

Route::prefix('announcement')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
    Route::post('/store', [AnnouncementController::class, 'store']);
    Route::post('/update', [AnnouncementController::class, 'update']);
    Route::post('/destroy', [AnnouncementController::class, 'destroy']);
    Route::post('/show', [AnnouncementController::class, 'show']);
});
