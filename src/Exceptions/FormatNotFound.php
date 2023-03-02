<?php

namespace Codedor\Attachments\Exceptions;

use Exception;

class FormatNotFound extends Exception
{
    public static function throw(string $name)
    {
        if (config('app.debug')) {
            throw new static("Format not found `$name`");
        }
    }
}
