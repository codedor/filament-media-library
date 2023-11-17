<?php

namespace Codedor\MediaLibrary\Conversions;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\WebP;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class LocalConversion implements Conversion
{
    public function convert(Attachment $attachment, Format $format, bool $force = false): bool
    {
        if (! is_convertible_image($attachment->extension)) {
            return false;
        }

        $formatName = $format->filename($attachment);
        $savePath = $attachment->absolute_directory_path . '/' . $formatName;

        if (array_key_exists('format', $format->definition()->toArray()[0])) {
            $savePath = Str::replaceLast(
                $attachment->extension,
                $format->definition()->toArray()[0]['format'],
                $savePath
            );
        }

        if (
            ! $force &&
            $attachment->getStorage()->exists("$attachment->directory/$formatName")
        ) {
            return false;
        }

        Image::load($attachment->absolute_file_path)
            ->manipulate($format->definition())
            ->save($savePath);

        if (WebP::isEnabled()) {
            Image::load($attachment->absolute_file_path)
                ->manipulate($format->definition())
                ->save(WebP::path($savePath, $attachment->extension));
        }

        return true;
    }
}
