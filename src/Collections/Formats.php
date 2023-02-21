<?php

namespace Codedor\Attachments\Collections;

use Closure;
use Codedor\Attachments\Entities\Format;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Formats extends Collection
{
    public function all(): self
    {
        foreach ($this->models() as $class) {
            if (! is_subclass_of($class, Model::class)) {
                continue;
            }

            if (! method_exists($class, 'getFormats')) {
                continue;
            }

            $class::getFormats($this);
        }

        return $this;
    }

    public function models(): array
    {
        return config('laravel-attachments.models', []);
    }

    public function register(Closure|Format $format): self
    {
        if ($format instanceof Closure) {
            $format($this);
        } else {
            $this->push($format);
        }

        return $this;
    }
}
