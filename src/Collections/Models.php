<?php

namespace Codedor\Attachments\Collections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Models
{
    public function __construct(
        protected Collection $models
    ) {
    }

    public function all(): Collection
    {
        return $this->models;
    }

    public function add($item)
    {
        if (! $this->modelIsRegisterable($item)) {
            return $this;
        }

        $this->models->add($item);

        \Codedor\Attachments\Facades\Formats::registerForModel($item);

        return $this;
    }

    protected function modelIsRegisterable(string $model): bool
    {
        return ! (! is_subclass_of($model, Model::class) ||
            ! method_exists($model, 'getFormats'));
    }
}
