<?php

namespace Codedor\Attachments\Conversions;

use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Models\Attachment;

interface Conversion
{
    public function convert(Attachment $attachment, Format $format);
}
