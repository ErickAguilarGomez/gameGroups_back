<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SpaAuthController;
use App\Http\Controllers\Auth\RegisterController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Incluir archivos por módulo para web routes
require __DIR__ . '/web/Auth/auth.php';

// Cualquier otra ruta pública web puede colocarse aquí o en archivos adicionales
