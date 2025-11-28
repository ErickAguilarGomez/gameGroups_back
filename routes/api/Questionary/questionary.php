<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Questionary\QuestionaryController;

Route::prefix('questionaries')->group(function () {
    Route::post('/', [QuestionaryController::class, 'index']);
    Route::post('/store', [QuestionaryController::class, 'store']);
    Route::patch('/update', [QuestionaryController::class, 'update']);
    Route::post('/destroy', [QuestionaryController::class, 'destroy']);
    Route::post('/show', [QuestionaryController::class, 'show']);
    Route::post('/show-with-stats', [QuestionaryController::class, 'showWithStats']);
    Route::post('/response/store', [QuestionaryController::class, 'storeResponse']);
    Route::patch('/response/update', [QuestionaryController::class, 'updateResponse']);
    Route::post('/response/destroy', [QuestionaryController::class, 'destroyResponse']);
    Route::post('/users-by-option', [QuestionaryController::class, 'getUsersByOption']);
});
