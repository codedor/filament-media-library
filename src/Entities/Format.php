<?php

namespace Codedor\Attachments\Entities;

use Codedor\Attachments\Interfaces\Formatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

abstract class Format implements Arrayable
{
    public Manipulations $manipulations;
    protected string $description;

    public function __construct() {
        $this->manipulations = new Manipulations();
    }

    public function prefix(): string
    {
        return Str::snake(class_basename(self::class));
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

    abstract public function definition(): Manipulations;
}
