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
        $filePath = "{$attachment->directory}/{$format->filename($attachment)}";

        if ($disk->providesTemporaryUrls()) {
            return $disk->temporaryUrl($filePath, now()->addMinutes(5));
        }

        $url = $disk->url($filePath);

        // Add cache-busting query parameter based on format's updated_at timestamp
        $attachmentFormat = $this->formats()->where('format', $format->key())->first();
        if ($attachmentFormat && $attachmentFormat->updated_at) {
            $timestamp = $attachmentFormat->updated_at->timestamp;
            $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . "v={$timestamp}";
        }

        return $url;
    }

    public function generateFormats(bool $force = false)
    {
        Formats::dispatchGeneration($this, $force);
    }
}
