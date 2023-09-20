<?php

namespace Codedor\MediaLibrary\Formats;

use Codedor\MediaLibrary\Models\Attachment;
use Spatie\Image\Manipulations;

class Thumbnail extends Format
{
    public bool $shownInFormatter = false;

    protected string $name = 'Thumbnail';

    protected string $description = 'Used in the CMS to display low-res images';

    public function definition(): Manipulations
    {
        return $this->manipulations->fit(Manipulations::FIT_CROP, 350, 350);
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(Attachment::class);
    }
}
