<?php

namespace Codedor\Attachments\Collections;

use Closure;
use Codedor\Attachments\Entities\Format;
use Codedor\Attachments\Facades\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Formats extends Collection
{
    public function all(): self
    {
        Models::reject(fn($class) => ! is_subclass_of($class, Model::class))
            ->reject(fn($class) => ! method_exists($class, 'getFormats'))
            ->map(fn($class) => $class::getFormats($this));

        return $this;
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
