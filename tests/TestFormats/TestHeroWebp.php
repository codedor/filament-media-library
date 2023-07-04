<?php

namespace Codedor\MediaLibrary\Tests\TestFormats;

use Codedor\MediaLibrary\Formats\Format;
use Spatie\Image\Manipulations;

class TestHeroWebp extends Format
{
    protected string $description = 'Test format';

    public function definition(): Manipulations
    {
        return $this->manipulations
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->format(Manipulations::FORMAT_WEBP)
            ->sepia();
    }
}
