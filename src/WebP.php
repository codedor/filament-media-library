<?php

namespace Codedor\MediaLibrary;

use Illuminate\Support\Str;

class WebP
{
    public static function isEnabled(): bool
    {
        return config('filament-media-library.enable-webp-generation', true);
    }

    public static function path(string $path, string $currentExtension): string
    {
        return Str::replaceLast(".$currentExtension", '.webp', $path);
    }
}
