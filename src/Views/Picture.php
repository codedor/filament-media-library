<?php

namespace Codedor\MediaLibrary\Views;

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\WebP;
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
        public bool $lazyload = false,
        public string $intersectModifier = 'threshold.50',
        public ?string $title = '',
        public ?string $lazyloadInitialFormat = 'thumbnail',
    ) {
        if ($this->format) {
            $this->getFormatClass();
        }

        if ($image->exists) {
            $this->hasWebp = method_exists($image, 'getWebpFormatOrOriginal') && $image->getWebpFormatOrOriginal($format);
        }
    }

    protected function getFormatClass()
    {
        $this->formatClass = Formats::exists($this->format);
    }

    public function width(): string|int|null
    {
        if (! $this->formatClass) {
            return $this->image->width;
        }

        if ($this->formatClass->height() && $this->formatClass->width()) {
            return $this->formatClass->width();
        }

        return $this->getDimension('width');
    }

    public function height(): string|int|null
    {
        if (! $this->formatClass) {
            return $this->image->height;
        }

        if ($this->formatClass->height() && $this->formatClass->width()) {
            return $this->formatClass->height();
        }

        return $this->getDimension('height');
    }

    public function render()
    {
        return $this->view('filament-media-library::components.picture');
    }

    public function getDimension(string $dimension): ?int
    {
        $filename = $this->formatClass->filename($this->image);
        $path = "{$this->image->absolute_directory_path}/{$filename}";

        if (WebP::isEnabled()) {
            $path = WebP::path(
                $path,
                $this->image->extension
            );
        }

        if (file_exists($path)) {
            $dimensions = getimagesize($path);

            return match ($dimension) {
                'width' => $dimensions[0],
                'height' => $dimensions[1],
                default => null,
            };
        }

        return null;
    }
}
