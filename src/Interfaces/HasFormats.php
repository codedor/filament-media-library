<?php

namespace Codedor\Attachments\Interfaces;

use Codedor\Attachments\Collections\Formats;
use Illuminate\Support\Collection;

interface HasFormats
{
    public static function getFormats(Collection $formats): Collection;
}
