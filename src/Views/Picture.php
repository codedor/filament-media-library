<?php

namespace Codedor\Attachments\Views;

use Codedor\Attachments\Facades\Formats;
use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Models\Attachment;
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
        $this->getFormatClass();
    }

    protected function getFormatClass()
    {
        $this->formatClass = Formats::exists($this->format);
    }

    public function render()
    {
        return $this->view('laravel-attachments::components.picture');
    }
}
