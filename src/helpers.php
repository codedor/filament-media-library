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
