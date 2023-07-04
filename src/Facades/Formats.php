<?php

namespace Codedor\MediaLibrary\Facades;

use Illuminate\Support\Facades\Facade;

class Formats extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Codedor\MediaLibrary\Collections\Formats::class;
    }
}
