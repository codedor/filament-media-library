<?php

namespace Codedor\MediaLibrary\Formats;

use Codedor\MediaLibrary\Models\Attachment;
use Spatie\Image\Drivers\ImageDriver;

class Lazyload extends Format
{
    public bool $shownInFormatter = false;

    protected string $name = 'Lazyload';

    protected string $description = 'Used as placeholder for lazy loaded images. These will only be
        shown for a short amount of time (and blurred) before the high quality image is loaded. This improves
        performance and load times.';

    public function definition(): Manipulations|ImageDriver
    {
        return $this->manipulations->width(50);
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(Attachment::class);
    }
}
