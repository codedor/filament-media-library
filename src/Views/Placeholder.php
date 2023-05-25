<?php

namespace Codedor\Attachments\Views;

class Placeholder extends Picture
{
    public function render()
    {
        return $this->view('laravel-attachments::components.placeholder');
    }
}
