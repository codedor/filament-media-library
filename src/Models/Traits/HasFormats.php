<?php

namespace Codedor\MediaLibrary\Models\Traits;

use Codedor\MediaLibrary\Exceptions\FormatNotFound;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Thumbnail;
use Codedor\MediaLibrary\Models\AttachmentFormat;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

        return $this->getFormat($name) ?: $this->url;
    }

    public function getFormat(string $name): ?string
    {
        $format = Formats::exists($name);

        if (! $format) {
            FormatNotFound::throw($name);

            return null;
        }

        return $this->getStorage()->url("{$this->directory}/{$format->filename($this)}");
    }

    public function generateFormats(bool $force = false)
    {
        Formats::dispatchGeneration($this, $force);
    }

    public static function getFormats(Collection $formats): Collection
    {
        return $formats->add(Thumbnail::make('thumbnail'));
    }
}
