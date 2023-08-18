<?php

namespace Codedor\MediaLibrary\Livewire;

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class FormatterModal extends Component
{
    public Attachment $attachment;

    public array $formats = [];

    #[On('filament-media-library::open-formatter-attachment-modal')]
    public function setAttachment(string $uuid, array $formats = null)
    {
        $this->attachment = Attachment::find($uuid);

        $this->formats = Formats::mapToKebab()
            ->when(! is_null($formats), fn ($allFormats) => $allFormats->filter(function (Format $format) use ($formats) {
                return in_array($format->key(), $formats);
            }))
            ->filter(fn (Format $format) => $format->shownInFormatter())
            ->map->toArray()
            ->toArray();
    }

    public function render()
    {
        $this->dispatch('filament-media-library::load-formatter', [
            'formats' => $this->formats,
        ]);

        $previousFormats = [];
        if (isset($this->attachment)) {
            $previousFormats = $this->attachment->formats()->pluck('data', 'format');
        }

        return view('filament-media-library::livewire.formatter-modal', [
            'previousFormats' => $previousFormats,
        ]);
    }

    public function saveCrop($event)
    {
        $format = Formats::findByKey($event['format']['key']);
        $filename = $format->filename($this->attachment);

        // Save the crop in the storage
        $crop = preg_replace('/data:image\/(.*?);base64,/', '', $event['crop']);
        $crop = base64_decode(str_replace(' ', '+', $crop));
        $this->attachment->getStorage()->put("{$this->attachment->directory}/{$filename}", $crop);

        // Save the crop on the attachment, for later adjustments
        $this->attachment->formats()->updateOrCreate(
            [
                'attachment_id' => $this->attachment->id,
                'format' => $event['format']['key'],
            ],
            ['data' => $event['data']]
        );

        Notification::make()
            ->title(__('filament-media-library::formatter.successfully formatted'))
            ->success()
            ->send();
    }
}