<?php

namespace Codedor\Attachments\Http\Livewire;

use Filament\Notifications\Notification;
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

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'laravel-attachment::upload-attachment-modal',
        ]);

        Notification::make()
            ->title(__('attachment.uploaded'))
            ->success()
            ->send();

        $this->emit('laravel-attachment::update-library');
    }

    public function render()
    {
        return view('laravel-attachments::livewire.upload-modal');
    }
}
