<?php

namespace Codedor\MediaLibrary\Formats;

use Codedor\MediaLibrary\Models\Attachment;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;

class Thumbnail extends Format
{
    public bool $shownInFormatter = false;

    protected string $name = 'Thumbnail';

    public function definition(): Manipulations|ImageDriver
    {
        return $this->manipulations->fit(Fit::Crop, 350, 350);
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(Attachment::class);
    }

    public function description(): string
    {
        return __('filament-media-library::formats.thumbnail description');
    }
}
