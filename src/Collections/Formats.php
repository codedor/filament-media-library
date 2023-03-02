<?php

namespace Codedor\Attachments\Collections;

use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Models\Attachment;
use Illuminate\Support\Collection;

class Formats extends Collection
{
    public function registerForModel(string $model): static
    {
        $this->put($model, $model::getFormats(new Collection()));

        return $this;
    }

    public function dispatchGeneration(Attachment $attachment): void
    {
        $this->flatten()
            ->each(function (Format $format) use ($attachment) {
                $format->conversion()->convert(
                    attachment: $attachment,
                    format: $format
                );
            });
    }
}
