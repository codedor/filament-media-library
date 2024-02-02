<?php

namespace Codedor\MediaLibrary\Views;

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\View\Component;

class Picture extends Component
{
    public ?Format $formatClass = null;

    public bool $hasWebp = false;

    public function __construct(
        public Attachment $image,
        public ?string $format = null,
        public ?string $alt = '',
        public bool $placeholder = false,
        public array $formats = [],
        public string $pictureClass = '',
        public string $class = '',
        public ?string $title = '',
        public bool $lazyload = true,
        public ?string $lazyloadInitialFormat = 'thumbnail',
    ) {
        if ($this->format) {
            $this->getFormatClass();
        }

        $this->hasWebp = method_exists($image, 'getWebpFormatOrOriginal') && $image->getWebpFormatOrOriginal($format);
    }

    protected function getFormatClass()
    {
        $this->formatClass = Formats::exists($this->format);
    }

    public function width(): ?string
    {
        if (! $this->formatClass) {
            return $this->image->width;
        }

        if ($this->formatClass->width() && $this->formatClass->height()) {
            return $this->formatClass->width();
        }

        // TODO BE: use width after crop when the format only has a height
        return null;
    }

    public function height(): ?string
    {
        if (! $this->formatClass) {
            return $this->image->height;
        }

        if ($this->formatClass->width() && $this->formatClass->height()) {
            return $this->formatClass->height();
        }

        // TODO BE: use height after crop when the format only has a height
        return null;
    }

    public function render()
    {
        return $this->view('filament-media-library::components.picture');
    }
}
