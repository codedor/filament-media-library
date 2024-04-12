<?php

namespace Codedor\MediaLibrary\Collections;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Jobs\GenerateAttachmentFormat;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Formats extends Collection
{
    public function registerFor(Format|string $format, string $model)
    {
        $formats = $this->get($model) ?? collect();

        $formats->push($format);

        $this->put($model, $formats);

        return $this;
    }

    public function register(string|array $formats): static
    {
        foreach (Arr::wrap($formats) as $format) {
            (new $format)->registerModelsForFormatter();
        }

        return $this;
    }

    public function exists(string $name): ?Format
    {
        return $this->mapToKebab()->get($name);
    }

    public function mapToKebab(): Collection
    {
        return $this->flatten()->mapWithKeys(fn (Format $format) => [
            $format->kebab() => $format,
        ]);
    }

    public function mapToClasses(): Collection
    {
        return $this->flatten()
            ->map(fn (Format $format) => get_class($format))
            ->unique()
            ->values();
    }

    public function dispatchGeneration(Attachment $attachment, bool $force = false): void
    {
        $this->flatten()->each(fn (Format $format) => dispatch(new GenerateAttachmentFormat(
            attachment: $attachment,
            format: $format,
            force: $force,
        )));
    }
}
