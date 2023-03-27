<?php

namespace Codedor\Attachments\Tests\TestConversions;

use Codedor\Attachments\Conversions\Conversion;
use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Models\Attachment;

class TestConversion implements Conversion
{
    public function convert(Attachment $attachment, Format $format): bool
    {
    }
}
