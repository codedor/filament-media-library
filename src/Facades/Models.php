<?php

namespace Codedor\Attachments\Facades;

use Illuminate\Support\Facades\Facade;

class Models extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Codedor\Attachments\Collections\Models::class;
    }
}
