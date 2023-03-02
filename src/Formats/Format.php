<?php

namespace Codedor\Attachments\Formats;

use Codedor\Attachments\Conversions\Conversion;
use Codedor\Attachments\Models\Attachment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Spatie\Image\Manipulations;

abstract class Format implements Arrayable
{
    public Manipulations $manipulations;

    protected string $description;

    public function __construct(
        protected string $column
    ) {
        $this->manipulations = new Manipulations();
    }

    public static function make(string $column): static
    {
        return new static($column);
    }

    public function prefix(): string
    {
        return Str::snake(class_basename(static::class)) . '__';
    }

    public function filename(Attachment $attachment): string
    {
        return $this->prefix() . $attachment->file_name;
    }

    public function toArray()
    {
        return [
            'manipulations' => $this->definition(),
            'description' => $this->description(),
        ];
    }

    public function description(): string
    {
        return $this->description;
    }

    public function conversion(): Conversion
    {
        return app(Conversion::class);
    }

    abstract public function definition(): Manipulations;
}
