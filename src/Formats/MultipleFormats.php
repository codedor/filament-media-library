<?php

namespace Codedor\MediaLibrary\Formats;

use Closure;
use Codedor\MediaLibrary\Conversions\Conversion;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Image\Manipulations;

abstract class MultipleFormats
{
    public array $models = [];
    public string $descriptionPrefix = '';

    abstract public function definitions(): Collection;

    public function registerModelsForFormatter(): void
    {
        $this->definitions()->each(function (Closure $definition, string $key) {
            $formatName = class_basename($this) . Str::ucfirst($key);

            collect($this->models)->each(function ($fields, $model) use ($formatName, $definition, $key) {
                if (is_int($model)) {
                    $model = $fields;
                    $fields = null;
                }

                $format = new class($fields) extends Format {};

                $format->setName($formatName);
                $format->setDescription("{$this->descriptionPrefix} {$key}");
                $format->manipulations = $definition();

                Formats::registerFor($format, $model);
            });
        });
    }
}
