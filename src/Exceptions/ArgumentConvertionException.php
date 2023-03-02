<?php

namespace Codedor\Attachments\Exceptions;

use Exception;

class ArgumentConvertionException extends Exception
{
    public static function invalid(string $manipulation): static
    {
        $message = "Invalid argument `$manipulation` for manipulation conversion.";

        return new static($message);
    }
}
