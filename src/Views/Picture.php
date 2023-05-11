<?php

namespace Codedor\Attachments\Views;

use Codedor\Attachments\Models\Attachment;
use Illuminate\View\Component;

class Picture extends Component
{
    public function __construct(
        public Attachment $attachment,
        public string $format
    ) {
    }

    public function render()
    {
        return $this->view('laravel-attachments::components.picture');
    }
}
