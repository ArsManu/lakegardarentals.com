<?php

namespace App\Support;

final class MediaUrl
{
    public static function public(?string $pathOrUrl): string
    {
        if ($pathOrUrl === null || $pathOrUrl === '') {
            return '';
        }
        if (str_starts_with($pathOrUrl, 'http://') || str_starts_with($pathOrUrl, 'https://')) {
            return $pathOrUrl;
        }

        if (config('filesystems.disks.public.driver') === 'local') {
            return '/storage/'.ltrim($pathOrUrl, '/');
        }

        $baseUrl = trim((string) config('filesystems.disks.public.url', ''));

        return $baseUrl !== ''
            ? rtrim($baseUrl, '/').'/'.ltrim($pathOrUrl, '/')
            : '/storage/'.ltrim($pathOrUrl, '/');
    }
}
