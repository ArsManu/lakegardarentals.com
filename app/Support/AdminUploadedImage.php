<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use RuntimeException;
use Throwable;

final class AdminUploadedImage
{
    public const MAX_WIDTH = 2500;

    public const WEBP_QUALITY = 82;

    /**
     * Resize to max width, encode WebP, store on disk. Returns path relative to disk root.
     */
    public static function storeAsWebp(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        // Decoding large camera JPEGs/PNG into bitmaps can exceed default 128M; scale happens after decode.
        // Do not lower memory_limit after processing: batch uploads need headroom until GC runs.
        @ini_set('memory_limit', '512M');

        $dir = trim($directory, '/');
        $manager = self::imageManager();

        $path = $file->getRealPath();
        if ($path === false || ! is_readable($path)) {
            throw new RuntimeException('Uploaded file is not readable.');
        }

        try {
            $image = $manager->read($path);
        } catch (Throwable $e) {
            throw new RuntimeException('Could not decode image.', 0, $e);
        }

        $image->scaleDown(width: self::MAX_WIDTH);

        try {
            $encoded = $image->toWebp(quality: self::WEBP_QUALITY);
        } catch (Throwable $e) {
            throw new RuntimeException('WebP encoding failed (check GD/Imagick WebP support).', 0, $e);
        }

        $relativePath = $dir.'/'.Str::uuid()->toString().'.webp';
        Storage::disk($disk)->put($relativePath, (string) $encoded);

        unset($image, $encoded);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        return $relativePath;
    }

    private static function imageManager(): ImageManager
    {
        if (extension_loaded('imagick')) {
            return ImageManager::imagick();
        }

        return ImageManager::gd();
    }
}
