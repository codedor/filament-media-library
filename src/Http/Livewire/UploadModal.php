<?php

namespace Codedor\Attachments\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class UploadModal extends Component
{
    use WithFileUploads;

    public $attachments = [];

    public function save()
    {
        foreach ($this->attachments as $attachment) {
            $attachment->save();
        }

        dd($this->attachments);
    }

    public function render()
    {
        return view('laravel-attachments::livewire.upload-modal');
    }
}
