<?php

namespace Codedor\Attachments\Collections;

use Illuminate\Support\Collection;

class Formats extends Collection
{
    public function registerForModel(string $model): static
    {
        $this->put($model, $model::getFormats(new Collection()));

        return $this;
    }
}
