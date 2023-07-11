<?php

namespace Codedor\MediaLibrary\Facades;

use Illuminate\Support\Facades\Facade;

class Models extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Codedor\MediaLibrary\Collections\Models::class;
    }
}
