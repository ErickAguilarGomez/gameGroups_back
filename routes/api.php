<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/Users/users.php';
    require __DIR__ . '/api/Photos/photos.php';
    require __DIR__ . '/api/Registrations/registrations.php';
    require __DIR__ . '/api/Groups/groups.php';
    require __DIR__ . '/api/Announcement/announcement.php';
});


// Public token creation (para backends externos) - el archivo tokens.php ya define /tokens/create
require __DIR__ . '/api/Tokens/tokens.php';
Route::get('/social-networks', [App\Http\Controllers\Api\SocialNetworkController::class, 'index']);
