<?php

namespace Codedor\MediaLibrary\Collections;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Jobs\GenerateAttachmentFormat;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Formats extends Collection
{
    public function registerForModel(string $model): static
    {
        $this->put($model, $model::getFormats(new Collection()));

        return $this;
    }

    public function exists(string $name): ?Format
    {
        return $this->mapToKebab()->get($name);
    }

    public function mapToKebab(): Collection
    {
        return $this->flatten()->mapWithKeys(fn (Format $format) => [
            Str::kebab(class_basename($format)) => $format,
        ]);
    }

    public function dispatchGeneration(Attachment $attachment, bool $force = false): void
    {
        $this->flatten()->each(fn (Format $format) => dispatch(new GenerateAttachmentFormat(
            attachment: $attachment,
            format: $format,
            force: $force,
        )));
    }

    public function findByKey(string $key): ?Format
    {
        return $this->flatten(1)->firstWhere(fn ($format) => get_class($format) === $key);
    }
}
