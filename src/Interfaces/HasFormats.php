<?php

namespace Codedor\Attachments\Interfaces;

use Codedor\Attachments\Collections\Formats;

interface HasFormats
{
    public static function getFormats(Formats $formats): void;
}
