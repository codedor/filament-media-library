<?php

use Codedor\MediaLibrary\Facades\Formats;

if (! function_exists('get_placeholder_url_by_format')) {
    function get_placeholder_url_by_format($format)
    {
        $format = Formats::exists($format);

        $width = $format?->width();
        $height = $format?->height();
        $name = $format?->name();

        return "https://via.placeholder.com/{$width}x{$height}/21348c/ffffff.webp?text={$name} {$width} x {$height}";
    }
}

if (! function_exists('is_convertable_image')) {
    function is_convertable_image(string $extension): bool
    {
        return in_array($extension, config('filament-media-library.extensions.image', [])) && $extension !== 'gif' && $extension !== 'svg';
    }
}

if (! function_exists('is_image_with_dimensions')) {
    function is_image_with_dimensions(string $extension): bool
    {
        return in_array($extension, config('filament-media-library.extensions.image', [])) && $extension !== 'svg';
    }
}
