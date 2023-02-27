<?php

namespace Codedor\Attachments\Collections;

use Illuminate\Support\Collection;

class Models extends Collection
{
    public function add($item = null)
    {
        if (! $item) {
            return $this;
        }

        if (is_array($item)) {
            foreach ($item as $model) {
                parent::add($model);
            }
        } else {
            parent::add($item);
        }

        return $this;
    }
}
