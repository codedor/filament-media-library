<?php

namespace Codedor\MediaLibrary\Formats;

use Codedor\MediaLibrary\Conversions\Conversion;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Spatie\Image\Manipulations;

abstract class Format implements Arrayable
{
    public Manipulations $manipulations;

    public bool $shownInFormatter = true;

    protected string $name;

    protected string $description;

    abstract public function definition(): Manipulations;

    public static function make(string $column = ''): static
    {
        return new static($column);
    }

    public function __construct(protected string $column)
    {
        $this->manipulations = new Manipulations();
        $this->definition();

        $this->name = Str::headline(class_basename(static::class));
    }

    public function filename(Attachment $attachment): string
    {
        return $this->prefix() . $attachment->file_name;
    }

    public function prefix(): string
    {
        return Str::snake(class_basename(static::class)) . '__';
    }

    public function toArray()
    {
        return [
            'key' => $this->key(),
            'name' => $this->name(),
            'description' => $this->description(),
            'width' => $this->width(),
            'height' => $this->height(),
            'aspectRatio' => $this->aspectRatio(),
            'shownInFormatter' => $this->shownInFormatter(),
        ];
    }

    public function key(): string
    {
        return get_class($this);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function column(): string
    {
        return $this->column;
    }

    public function argument(string $argument): mixed
    {
        return $this->manipulations->getManipulationArgument($argument);
    }

    public function width(): string
    {
        return $this->argument('width');
    }

    public function height(): string
    {
        return $this->argument('height');
    }

    public function aspectRatio(): string
    {
        return $this->width() / $this->height();
    }

    public function description(): string
    {
        return $this->description;
    }

    public function shownInFormatter(): bool
    {
        return $this->shownInFormatter;
    }

    public function conversion(): Conversion
    {
        return app(Conversion::class);
    }
}
