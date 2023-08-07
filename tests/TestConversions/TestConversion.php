<?php

namespace Codedor\MediaLibrary\Tests\TestConversions;

use Codedor\MediaLibrary\Conversions\Conversion;
use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;

class TestConversion implements Conversion
{
    public function convert(Attachment $attachment, Format $format, bool $force = false): bool
    {
    }
}
