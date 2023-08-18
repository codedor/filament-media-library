<?php

namespace Codedor\MediaLibrary\Views;

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\View\Component;

class Picture extends Component
{
    public ?Format $formatClass = null;

    public function __construct(
        public Attachment $image,
        public ?string $format = null,
        public string $alt = '',
        public bool $placeholder = false,
        public array $formats = [],
        public string $pictureClass = '',
        public string $class = '',
        public string $title = '',
        public bool $lazyload = true,
    ) {
        if ($this->format) {
            $this->getFormatClass();
        }
    }

    protected function getFormatClass()
    {
        $this->formatClass = Formats::exists($this->format);
    }

    public function width(): ?string
    {
        return $this->formatClass ? $this->formatClass->width() : $this->image->width;
    }

    public function height(): ?string
    {
        return $this->formatClass ? $this->formatClass->height() : $this->image->height;
    }

    public function render()
    {
        return $this->view('filament-media-library::components.picture');
    }
}
