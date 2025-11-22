<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CloudinaryController extends Controller
{
    /**
     * Generar firma para upload de Cloudinary
     */
    public function generateSignature(Request $request)
    {
        $timestamp = time();
        $folder = $request->input('folder', 'user_photos');
        
        $params = [
            'timestamp' => $timestamp,
            'folder' => $folder,
        ];

        $paramString = '';
        ksort($params);
        foreach ($params as $key => $value) {
            $paramString .= $key . '=' . $value . '&';
        }
        $paramString = rtrim($paramString, '&');
        $signature = hash('sha256', $paramString . env('CLOUDINARY_API_SECRET'));
        return response()->json([
            'signature' => $signature,
            'timestamp' => $timestamp,
            'api_key' => env('CLOUDINARY_API_KEY'),
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'folder' => $folder,
        ]);
    }
}
