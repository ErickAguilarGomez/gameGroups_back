<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cloudinary\CloudinaryController;

Route::post('/cloudinary/signature', [CloudinaryController::class, 'generateSignature']);
