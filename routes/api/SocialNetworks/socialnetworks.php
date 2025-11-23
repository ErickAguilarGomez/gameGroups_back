<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialNetwork\SocialNetworkController;

Route::get('/social-networks', [SocialNetworkController::class, 'index']);
