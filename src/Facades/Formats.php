<?php

namespace Codedor\Attachments\Facades;

use Illuminate\Support\Facades\Facade;

class Formats extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Codedor\Attachments\Collections\Formats::class;
    }
}
