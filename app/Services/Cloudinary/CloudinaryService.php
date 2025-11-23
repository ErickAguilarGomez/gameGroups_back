<?php

namespace App\Services\Cloudinary;

use App\Repositories\Cloudinary\CloudinaryRepository;

class CloudinaryService
{
    protected $cloudinaryRepository;

    public function __construct(CloudinaryRepository $cloudinaryRepository)
    {
        $this->cloudinaryRepository = $cloudinaryRepository;
    }

    public function generateSignature($request)
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

        return [
            'signature' => $signature,
            'timestamp' => $timestamp,
            'api_key' => env('CLOUDINARY_API_KEY'),
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'folder' => $folder,
        ];
    }
}
