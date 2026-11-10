<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Allowed mime types for security.
     */
    protected static array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
        'image/gif',
    ];

    /**
     * Maximum file size in kilobytes (2MB).
     */
    protected static int $maxKilobytes = 2048;

    /**
     * Upload an image securely to public disk.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $oldPath
     * @return string Relative stored path
     */
    public static function upload(UploadedFile $file, string $folder = 'website', ?string $oldPath = null): string
    {
        // 1. Delete previous file if provided
        if (!empty($oldPath)) {
            static::delete($oldPath);
        }

        // 2. Generate safe random filename
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $safeName = Str::random(32) . '.' . strtolower($extension);

        // 3. Store in public disk
        return $file->storeAs("uploads/{$folder}", $safeName, 'public');
    }

    /**
     * Delete an existing image file from public disk safely.
     *
     * @param string|null $path
     * @return bool
     */
    public static function delete(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // Prevent path traversal
        $normalized = ltrim(str_replace(['../', '..\\'], '', $path), '/');

        if (Storage::disk('public')->exists($normalized)) {
            return Storage::disk('public')->delete($normalized);
        }

        return false;
    }

    /**
     * Return public URL for an uploaded file or a fallback.
     *
     * @param string|null $path
     * @param string|null $defaultFallback
     * @return string|null
     */
    public static function url(?string $path, ?string $defaultFallback = null): ?string
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $defaultFallback;
    }
}
