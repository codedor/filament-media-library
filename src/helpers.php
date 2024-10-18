<?php

use Codedor\MediaLibrary\Facades\Formats;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Resource;

if (! function_exists('get_placeholder_url_by_format')) {
    function get_placeholder_url_by_format($format)
    {
        $format = Formats::exists($format);

        $width = $format?->width();
        $height = $format?->height();
        $name = $format?->name();

        return "https://via.placeholder.com/{$width}x{$height}/edeced/edeced.webp";
    }
}

if (! function_exists('is_convertible_image')) {
    function is_convertible_image(string $extension): bool
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

if (! function_exists('get_resource_url_by_model')) {
    function get_resource_url_by_model(Model $model, $action = 'index')
    {
        $resourceClass = get_class($model);

        $filamentResource = collect(Filament::getResources())
            ->first(function (Resource $filamentResource) use ($resourceClass) {
                return $filamentResource::getModel() === $resourceClass;
            });

        if (! $filamentResource) {
            return null;
        }

        if ($action === 'index') {
            return $filamentResource::getUrl();
        }

        return $filamentResource::getUrl($action, $model);
    }
}
