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

    public ?array $modelFormats = null;

    #[On('filament-media-library::open-formatter-attachment-modal')]
    public function setAttachment(string $uuid = '', array $formats = null)
    {
        $this->attachment = Attachment::find($uuid);
        $this->modelFormats = $formats;
    }

    public function render()
    {
        $this->dispatch('filament-media-library::load-formatter');

        $formats = Formats::mapToKebab()
            ->filter(fn (Format $format) => $format->shownInFormatter());

        if (! is_null($this->modelFormats)) {
            $formats = $formats->filter(fn ($format) => in_array(
                get_class($format),
                $this->modelFormats
            ));
        }

        $previousFormats = [];
        if (isset($this->attachment)) {
            $previousFormats = $this->attachment->formats()->pluck('data', 'format');
        }

        return view('filament-media-library::livewire.formatter-modal', [
            'formats' => $formats->map->toArray(),
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
