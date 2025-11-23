<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Announcement\AnnouncementController;

Route::prefix('announcements')->group(function () {
    Route::post('/', [AnnouncementController::class, 'index']);
    Route::post('/store', [AnnouncementController::class, 'store']);
    Route::patch('/update', [AnnouncementController::class, 'update']);
    Route::post('/destroy', [AnnouncementController::class, 'destroy']);
    Route::post('/show', [AnnouncementController::class, 'show']);
});
