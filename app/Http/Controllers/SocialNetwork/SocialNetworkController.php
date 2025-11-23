<?php

namespace App\Http\Controllers\SocialNetwork;

use App\Http\Controllers\Controller;
use App\Services\SocialNetwork\SocialNetworkService;
use Illuminate\Http\Request;

class SocialNetworkController extends Controller
{
    protected $socialNetworkService;

    public function __construct(SocialNetworkService $socialNetworkService)
    {
        $this->socialNetworkService = $socialNetworkService;
    }

    /**
     * Obtener todas las redes sociales
     */
    public function index()
    {
        $socialNetworks = $this->socialNetworkService->getAll();

        return response()->json($socialNetworks);
    }
}
