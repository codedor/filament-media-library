<?php

namespace Codedor\Attachments\Formats;

use Codedor\Attachments\Entities\Manipulations;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

abstract class Format implements Arrayable
{
    public Manipulations $manipulations;

    protected string $description;

    public function __construct(
        protected string $column
    ){
        $this->manipulations = new Manipulations();
    }

    public static function make(string $column): static
    {
        return new static($column);
    }

    public function prefix(): string
    {
        return Str::snake(class_basename(static::class));
    }

    public function toArray()
    {
        return [
            'manipulations' => $this->definition(),
            'description' => $this->description(),
        ];
    }

    abstract public function definition(): Manipulations;

    public function description(): string
    {
        return $this->description;
    }
}
