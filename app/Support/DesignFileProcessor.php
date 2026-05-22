<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;
use Spatie\Image\Enums\Fit;

class DesignFileProcessor
{
    /**
     * Process a design image file stored in the public disk:
     * - Resize to a max of 2000px on the longest side (preserving aspect ratio)
     * - Convert to WebP for consistent browser display
     * - Generate a 400px thumbnail alongside the original
     *
     * Returns the (possibly new) stored path for the processed image.
     * Non-image files (PDF, etc.) are returned as-is.
     */
    public static function process(string $storedPath): string
    {
        $fullPath = Storage::disk('public')->path($storedPath);

        if (! file_exists($fullPath)) return $storedPath;

        $ext = strtolower(pathinfo($storedPath, PATHINFO_EXTENSION));

        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'])) {
            // Not an image (PDF, EPS, etc.) — leave untouched
            return $storedPath;
        }

        // Convert to webp and resize to max 2000px
        $webpPath   = preg_replace('/\.[^.]+$/', '.webp', $storedPath);
        $webpFull   = Storage::disk('public')->path($webpPath);
        $thumbPath  = preg_replace('/(\.[^.]+)$/', '_thumb.webp', $storedPath);
        $thumbFull  = Storage::disk('public')->path($thumbPath);

        try {
            // Full-size optimised version
            Image::load($fullPath)
                ->fit(Fit::Max, 2000, 2000)
                ->optimize()
                ->save($webpFull);

            // Thumbnail
            Image::load($fullPath)
                ->fit(Fit::Crop, 400, 400)
                ->save($thumbFull);

            // Remove original only if we produced a different file
            if ($webpPath !== $storedPath && file_exists($fullPath)) {
                @unlink($fullPath);
            }

            return $webpPath;
        } catch (\Throwable $e) {
            // Processing failed — return original path so the upload still works
            \Illuminate\Support\Facades\Log::warning('[DesignFileProcessor] ' . $e->getMessage());
            return $storedPath;
        }
    }

    /**
     * Return the thumbnail path for a given design file path, if it exists.
     */
    public static function thumbPath(string $storedPath): ?string
    {
        $thumbPath = preg_replace('/(\.[^.]+)$/', '_thumb.webp', $storedPath);
        if (Storage::disk('public')->exists($thumbPath)) {
            return $thumbPath;
        }
        return null;
    }
}
