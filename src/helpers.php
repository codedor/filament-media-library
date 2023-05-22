<?php

if (! function_exists('get_placeholder_url_by_format')) {
    function get_placeholder_url_by_format($format)
    {
        // TODO BE: Get width, height and name from format
        $width = 300;
        $height = 500;
        $name = 'test';

        $text = urlencode("{$name}\r\n{$width} x {$height}");

        return "https://via.placeholder.com/{$width}x{$height}/21348c/ffffff.webp?text={$text}";
    }
}
