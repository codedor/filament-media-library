<?php

namespace Codedor\Attachments\Collections;

use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Jobs\GenerateAttachmentFormat;
use Codedor\Attachments\Models\Attachment;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Formats extends Collection
{
    public function registerForModel(string $model): static
    {
        $this->put($model, $model::getFormats(new Collection()));

        return $this;
    }

    public function exists(string $name): null|Format
    {
        return $this->mapToKebab()->get($name);
    }

    public function mapToKebab(): Collection
    {
        return $this->flatten()->mapWithKeys(fn(Format $format) => [
            Str::kebab(class_basename($format)) => $format,
        ]);
    }

    public function dispatchGeneration(Attachment $attachment): void
    {
        $this->flatten()
            ->each(function (Format $format) use ($attachment) {
                dispatch(new GenerateAttachmentFormat(
                    attachment: $attachment,
                    format: $format
                ));
            });
    }
}
