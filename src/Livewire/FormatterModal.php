<?php

namespace Codedor\MediaLibrary\Livewire;

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\WebP;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Image\Image;

class FormatterModal extends Component
{
    public Attachment $attachment;

    public array $formats = [];

    public ?array $currentFormat = null;

    #[On('filament-media-library::open-formatter-attachment-modal')]
    public function setAttachment(string $uuid, ?array $formats = null)
    {
        $this->attachment = Attachment::find($uuid);

        $formats = Collection::wrap($formats ?? Formats::mapToClasses())
            ->map(fn ($format) => $format::make())
            ->filter(fn (Format $format) => $format->shownInFormatter())
            ->map->toArray();

        // Make sure we have the correct format selected when switching fields
        if ($this->currentFormat && ! $formats->pluck('key')->contains($this->currentFormat['key'])) {
            $this->currentFormat = null;
        }

        $this->currentFormat ??= $formats->first();

        $this->formats = $formats->toArray();
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
        $format = $event['format']['key']::make();
        $filename = $format->filename($this->attachment);

        // Save the crop in the storage
        $crop = preg_replace('/data:image\/(.*?);base64,/', '', $event['crop']);
        $crop = base64_decode(str_replace(' ', '+', $crop));
        $this->attachment->getStorage()->put("{$this->attachment->directory}/{$filename}", $crop);

        if (WebP::isEnabled()) {
            Image::load("{$this->attachment->absolute_directory_path}/{$filename}")
                ->format('webp')
                ->save(WebP::path(
                    "{$this->attachment->absolute_directory_path}/{$filename}",
                    $this->attachment->extension
                ));
        }

        // Save the crop on the attachment, for later adjustments
        $this->attachment->formats()->updateOrCreate([
            'attachment_id' => $this->attachment->id,
            'format' => $event['format']['key'],
        ], [
            'data' => $event['data'],
        ]);

        Notification::make()
            ->title(__('filament-media-library::formatter.successfully formatted'))
            ->success()
            ->send();
    }
}
