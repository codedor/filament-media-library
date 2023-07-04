<?php

namespace Codedor\MediaLibrary\Http\Livewire;

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Notifications\Notification;
use Livewire\Component;

class FormatterModal extends Component
{
    public Attachment $attachment;

    public null|array $modelFormats = null;

    protected $listeners = [
        'filament-media-library::open-formatter-attachment-modal' => 'setAttachment',
        'cropped' => 'saveCrop',
    ];

    public function setAttachment(string $uuid = '', null|array $formats = null)
    {
        $this->attachment = Attachment::find($uuid);
        $this->modelFormats = $formats;
    }

    public function render()
    {
        $this->dispatchBrowserEvent('filament-media-library::load-formatter');

        $formats = Formats::mapToKebab();

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
            ->title(__('filament_media.successfully formatted'))
            ->success()
            ->send();
    }
}
