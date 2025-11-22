<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\PhotoReviewController;
use App\Http\Controllers\RegistrationReviewController;
use App\Http\Controllers\GroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ========================================
// RUTAS PÚBLICAS (sin autenticación)
// ========================================
// Ejemplo: endpoints públicos como /api/status
Route::get('/status', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Redes sociales (público para el registro)
Route::get('/social-networks', [App\Http\Controllers\Api\SocialNetworkController::class, 'index']);

// ========================================
// AUTENTICACIÓN MIXTA (Cookies SPA o Tokens Bearer)
// auth:sanctum funciona con AMBOS métodos gracias a EnsureFrontendRequestsAreStateful
// ========================================
Route::middleware('auth:sanctum')->group(function () {
    // Cargar grupos de rutas protegidas por auth
    require __DIR__ . '/api/Users/users.php';
    require __DIR__ . '/api/Photos/photos.php';
    require __DIR__ . '/api/Registrations/registrations.php';
    require __DIR__ . '/api/Groups/groups.php';
});

// ========================================
// RUTAS PÚBLICAS (sin autenticación) / endpoints sueltos
// ========================================
// Status público
Route::get('/status', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Public token creation (para backends externos) - el archivo tokens.php ya define /tokens/create
require __DIR__ . '/api/Tokens/tokens.php';

