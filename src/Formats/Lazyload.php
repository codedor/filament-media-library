<?php

namespace Wotz\MediaLibrary\Formats;

use Wotz\MediaLibrary\Models\Attachment;
use Spatie\Image\Drivers\ImageDriver;

class Lazyload extends Format
{
    public bool $shownInFormatter = false;

    protected string $name = 'Lazyload';

    public function definition(): Manipulations|ImageDriver
    {
        return $this->manipulations->width(50);
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(Attachment::class);
    }

    public function description(): string
    {
        return __('filament-media-library::formats.lazyload description');
    }
}
