<?php

namespace Codedor\MediaLibrary\Models\Traits;

use Codedor\MediaLibrary\Exceptions\FormatNotFound;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\AttachmentFormat;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasFormats
{
    public function formats(): HasMany
    {
        return $this->hasMany(AttachmentFormat::class);
    }

    public function getFormatOrOriginal(?string $name): string
    {
        if (! $name) {
            return $this->url;
        }

        return $this->getFormat($name) ?? $this->url;
    }

    public function getFormat(string $name, ?string $extension = null): ?string
    {
        if (! is_convertible_image($this->extension)) {
            return $this->url;
        }

        $attachment = clone $this;
        if ($extension) {
            $attachment->extension = $extension;
        }

        $format = Formats::exists($name);

        if (! $format) {
            FormatNotFound::throw($name);

            return null;
        }

        $disk = $attachment->getStorage();

        if ($disk->providesTemporaryUrls()) {
            return $disk->temporaryUrl(
                "{$attachment->directory}/{$format->filename($attachment)}",
                now()->addMinutes(5)
            );
        }

        return $disk->url(
            "{$attachment->directory}/{$format->filename($attachment)}"
        );
    }

    public function generateFormats(bool $force = false)
    {
        Formats::dispatchGeneration($this, $force);
    }
}
