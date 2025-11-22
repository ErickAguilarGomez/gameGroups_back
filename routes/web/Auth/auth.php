<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SpaAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CloudinaryController;

// Rutas públicas relacionadas con autenticación / registro (SPA cookie-based)
Route::post('/login', [SpaAuthController::class, 'login']);
Route::post('/logout', [SpaAuthController::class, 'logout']);
Route::post('/register', [RegisterController::class, 'register']);

// Cloudinary signature (pública)
Route::post('/cloudinary/signature', [CloudinaryController::class, 'generateSignature']);
