<?php

namespace Codedor\Attachments\Http\Livewire;

use Codedor\Attachments\Facades\Formats;
use Codedor\Attachments\Models\Attachment;
use Filament\Notifications\Notification;
use Livewire\Component;

class FormatterModal extends Component
{
    public Attachment $attachment;

    protected $listeners = [
        'laravel-attachment::open-formatter-attachment-modal' => 'setAttachment',
        'cropped' => 'saveCrop',
    ];

    public function setAttachment(string $uuid = '')
    {
        $this->attachment = Attachment::find($uuid);
    }

    public function render()
    {
        $this->dispatchBrowserEvent('laravel-attachments::load-formatter');

        $formats = Formats::mapToKebab()->map->toArray();

        $previousFormats = [];
        if (isset($this->attachment)) {
            $previousFormats = $this->attachment->formats()->pluck('data', 'format');
        }

        return view('laravel-attachments::livewire.formatter-modal', [
            'formats' => $formats,
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
            ->title(__('attachment.successfully formatted'))
            ->success()
            ->send();
    }
}
