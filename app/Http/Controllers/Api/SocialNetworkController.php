<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialNetwork;
use Illuminate\Http\Request;

class SocialNetworkController extends Controller
{
    /**
     * Obtener todas las redes sociales
     */
    public function index()
    {
        $socialNetworks = SocialNetwork::all();
        
        return response()->json($socialNetworks);
    }
}
