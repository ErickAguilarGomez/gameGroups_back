<?php

namespace App\Http\Controllers\Cloudinary;

use App\Http\Controllers\Controller;
use App\Services\Cloudinary\CloudinaryService;
use Illuminate\Http\Request;

class CloudinaryController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Generar firma para upload de Cloudinary
     */
    public function generateSignature(Request $request)
    {
        $result = $this->cloudinaryService->generateSignature($request);
        return response()->json($result);
    }
}
