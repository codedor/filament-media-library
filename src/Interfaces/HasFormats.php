<?php

namespace Codedor\MediaLibrary\Interfaces;

use Illuminate\Support\Collection;

interface HasFormats
{
    public static function getFormats(Collection $formats): Collection;
}
