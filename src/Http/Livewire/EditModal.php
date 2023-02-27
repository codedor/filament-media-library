<?php

namespace Codedor\Attachments\Http\Livewire;

use Codedor\Attachments\Models\Attachment;
use Codedor\Attachments\Models\AttachmentTag;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class EditModal extends Component implements HasForms
{
    use InteractsWithForms;

    public null|Attachment $attachment = null;

    public array $fields = [];

    protected bool $isCachingForms = false;

    protected $listeners = [
        'laravel-attachment::open-edit-attachment-modal' => 'setAttachment',
        'laravel-attachment::close-edit-attachment-modal' => 'setAttachment',
    ];

    public function mount()
    {
        $this->form->fill();
    }

    public function setAttachment(string $uuid = '')
    {
        $this->attachment = Attachment::find($uuid);
        if (! $this->attachment) {
            return;
        }

        $this->form->fill(['fields' => [
            'filename' => $this->attachment->name,
            'alt' => $this->attachment->alt,
            'caption' => $this->attachment->caption,
            'tags' => $this->attachment->tags->pluck('id')->toArray(),
        ]]);
    }

    public function render()
    {
        return view('laravel-attachments::livewire.edit-modal');
    }

    public function submit()
    {
        $this->validate();

        $this->attachment->update($this->fields);
        $this->attachment->tags()->sync($this->fields['tags'] ?? []);

        $this->emit('laravel-attachment::update-library');
        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'laravel-attachment::edit-attachment-modal',
        ]);

        Notification::make()
            ->title(__('attachment.successfully updated'))
            ->success()
            ->send();
    }

    protected function getFormSchema(): array
    {
        if (is_null($this->attachment)) {
            return [];
        }

        return [
            TextInput::make('fields.translated_filename'),

            TextInput::make('fields.alt'),

            TextInput::make('fields.caption'),

            Select::make('fields.tags')
                ->label(__('laravel-attachment::tags'))
                ->options(AttachmentTag::limit(50)->pluck('title', 'id'))
                ->searchable()
                ->multiple(),
        ];
    }
}
