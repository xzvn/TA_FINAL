<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryService
{
    private static bool $configured = false;

    private static function configure(): void
    {
        if (self::$configured) {
            return;
        }

        $cloudName = trim((string) config('cloudinary.cloud_name'));
        $apiKey = trim((string) config('cloudinary.api_key'));
        $apiSecret = trim((string) config('cloudinary.api_secret'));

        $missing = [];

        if ($cloudName === '') {
            $missing[] = 'CLOUDINARY_CLOUD_NAME';
        }

        if ($apiKey === '') {
            $missing[] = 'CLOUDINARY_API_KEY';
        }

        if ($apiSecret === '') {
            $missing[] = 'CLOUDINARY_API_SECRET';
        }

        if ($missing !== []) {
            throw new RuntimeException(
                'Konfigurasi Cloudinary belum lengkap: ' . implode(', ', $missing)
            );
        }

        Configuration::instance([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true,
            ],
        ]);

        self::$configured = true;
    }

    public static function uploadImage(UploadedFile $file, string $folder): string
    {
        self::configure();

        $result = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => trim($folder, '/'),
            'resource_type' => 'image',
            'use_filename' => true,
            'unique_filename' => true,
            'overwrite' => false,
        ]);

        $url = (string) ($result['secure_url'] ?? '');

        if ($url === '') {
            throw new RuntimeException('Cloudinary tidak mengembalikan URL gambar.');
        }

        return $url;
    }

    public static function uploadFile(UploadedFile $file, string $folder): string
    {
        self::configure();

        $result = (new UploadApi())->upload($file->getRealPath(), [
            'folder' => trim($folder, '/'),
            'resource_type' => 'auto',
            'use_filename' => true,
            'unique_filename' => true,
            'overwrite' => false,
        ]);

        $url = (string) ($result['secure_url'] ?? '');

        if ($url === '') {
            throw new RuntimeException('Cloudinary tidak mengembalikan URL file.');
        }

        return $url;
    }

    public static function mediaUrl(?string $path): ?string
    {
        if (! is_string($path)) {
            return null;
        }

        $path = html_entity_decode($path, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $path = trim($path, " \t\n\r\0\x0B\"'");
        $path = str_replace('\\', '/', $path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, '//')) {
            return 'https:' . $path;
        }

        if (preg_match('#^res\.cloudinary\.com/#i', $path)) {
            return 'https://' . $path;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $host = strtolower((string) parse_url($path, PHP_URL_HOST));

            if ($host === 'res.cloudinary.com' && str_starts_with(strtolower($path), 'http://')) {
                return 'https://' . substr($path, 7);
            }

            if (app()->environment('production') && str_starts_with(strtolower($path), 'http://')) {
                $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

                if ($appHost !== '' && $host === $appHost) {
                    return 'https://' . substr($path, 7);
                }
            }

            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}
