<?php

namespace Codedor\MediaLibrary\Http\Livewire;

use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\Translatable;
use Livewire\Component;

class EditModal extends Component implements HasForms
{
    use InteractsWithForms;
    use Translatable;

    public ?Attachment $attachment = null;

    public array $fields = [];

    protected bool $isCachingForms = false;

    protected $listeners = [
        'filament-media-library::open-edit-attachment-modal' => 'setAttachment',
        'filament-media-library::close-edit-attachment-modal' => 'setAttachment',
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
            'translated_name' => $this->attachment->translated_name,
            'alt' => $this->attachment->alt,
            'caption' => $this->attachment->caption,
            'tags' => $this->attachment->tags->pluck('id')->toArray(),
        ]]);
    }

    public function render()
    {
        return view('filament-media-library::livewire.edit-modal');
    }

    public function submit()
    {
        $this->validate();

        $this->attachment->update($this->fields);
        $this->attachment->tags()->sync($this->fields['tags'] ?? []);

        $this->emit('filament-media-library::update-library');
        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'filament-media-library::edit-attachment-modal',
        ]);

        Notification::make()
            ->title(__('filament_media.successfully updated'))
            ->success()
            ->send();
    }

    protected function getFormSchema(): array
    {
        if (is_null($this->attachment)) {
            return [];
        }

        return [
            TextInput::make('fields.translated_name'),

            TextInput::make('fields.alt'),

            TextInput::make('fields.caption'),

            Select::make('fields.tags')
                ->label(__('filament_media.tags'))
                ->options(AttachmentTag::limit(50)->pluck('title', 'id'))
                ->searchable()
                ->multiple(),
        ];
    }
}
