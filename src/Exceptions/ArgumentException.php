<?php

namespace Codedor\Attachments\Exceptions;

use Exception;
use Illuminate\Support\Collection;

class ArgumentException extends Exception
{
    public static function invalid(string $manipulation, string $value, Collection $options = null): static
    {
        $message = "Invalid argument `$value` for manipulation `$manipulation`.";

        if ($options) {
            $options = $options->implode(', ');

            $message .= " Possible options are: $options";
        }

        return new static($message);
    }
}
