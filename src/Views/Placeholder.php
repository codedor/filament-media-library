<?php

namespace Codedor\Attachments\Views;

class Placeholder extends Picture
{
    public function render()
    {
        dd('sdf');

        return $this->view('laravel-attachments::components.placeholder-picture');
    }
}
