<?php

namespace Codedor\Attachments\Tests\TestFormats;

use Codedor\Attachments\Formats\Format;
use Spatie\Image\Manipulations;

class TestHero extends Format
{
    protected string $description = 'Test format';

    public function definition(): Manipulations
    {
        return $this->manipulations
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->sepia();
    }
}
