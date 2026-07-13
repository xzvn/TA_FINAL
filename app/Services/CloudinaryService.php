<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private static function configure(): void
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    public static function uploadImage(UploadedFile $file, string $folder): string
    {
        self::configure();

        $result = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'image',
            'use_filename' => true,
            'unique_filename' => true,
            'overwrite' => false,
        ]);

        return $result['secure_url'];
    }


    public static function uploadFile(UploadedFile $file, string $folder): string
    {
        self::configure();

        $result = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => $folder,
            'resource_type' => 'auto',
            'use_filename' => true,
            'unique_filename' => true,
            'overwrite' => false,
        ]);

        return $result['secure_url'];
    }

    public static function mediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return str_starts_with($path, 'http')
            ? $path
            : asset('storage/' . $path);
    }
}
