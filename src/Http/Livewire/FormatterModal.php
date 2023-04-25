<?php

namespace Codedor\Attachments\Http\Livewire;

use Codedor\Attachments\Facades\Formats;
use Codedor\Attachments\Models\Attachment;
use Livewire\Component;

class FormatterModal extends Component
{
    public null|string $currentFormatName = null;

    public Attachment $attachment;

    protected $listeners = [
        'laravel-attachment::open-formatter-attachment-modal' => 'setAttachment',
    ];

    public function setAttachment(string $uuid = '')
    {
        $this->attachment = Attachment::find($uuid);

        $this->dispatchBrowserEvent('laravel-attachments::load-formatter');
    }

    public function render()
    {
        $formats = Formats::mapToKebab();

        $this->currentFormatName ??= $formats->keys()->first();

        return view('laravel-attachments::livewire.formatter-modal', [
            'formats' => $formats,
            'currentFormat' => $formats[$this->currentFormatName],
        ]);
    }
}
