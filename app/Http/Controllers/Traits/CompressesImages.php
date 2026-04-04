<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;

trait CompressesImages
{
    /**
     * Compress and convert an uploaded image to WebP format.
     * Supports: jpg, jpeg, png, gif, bmp, tiff, heic, heif, webp
     *
     * @param UploadedFile $file
     * @param string $directory Storage subdirectory
     * @param int $quality WebP quality (1-100). Default 30 for ~70% size reduction.
     * @return string The stored file path relative to 'public' disk.
     */
    protected function compressToWebp(UploadedFile $file, string $directory = 'uploads', int $quality = 30): string
    {
        // Use Imagick driver for HEIC/HEIF support
        $manager = ImageManager::imagick();

        // Read the uploaded image
        $image = $manager->read($file->getRealPath());

        // Encode to WebP with specified quality (~70% compression)
        $encoded = $image->encode(new WebpEncoder(quality: $quality));

        // Generate unique filename with .webp extension
        $filename = $directory . '/' . Str::uuid() . '.webp';

        // Store the compressed WebP image
        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}
