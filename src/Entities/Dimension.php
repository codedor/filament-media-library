<?php

namespace Codedor\Attachments\Entities;

class Dimension
{
    public int $height;

    public int $width;

    public function __construct(
        public string $path
    ) {
        [$this->height, $this->width] = @getimagesize($path);
    }

    public static function for(string $path): self
    {
        return new self($path);
    }
}
