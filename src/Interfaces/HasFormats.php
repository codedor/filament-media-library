<?php

namespace Codedor\Attachments\Interfaces;

use Illuminate\Support\Collection;

interface HasFormats
{
    public static function getFormats(Collection $formats): Collection;
}
