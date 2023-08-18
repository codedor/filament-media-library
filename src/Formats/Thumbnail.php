<?php

namespace Codedor\MediaLibrary\Formats;

use Spatie\Image\Manipulations;

class Thumbnail extends Format
{
    public bool $shownInFormatter = false;

    public function definition(): Manipulations
    {
        return $this->manipulations->fit(Manipulations::FIT_CROP, 350, 350);
    }
}
