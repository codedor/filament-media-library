<?php

namespace Codedor\MediaLibrary\Conversions;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;

interface Conversion
{
    public function convert(Attachment $attachment, Format $format): bool;
}
