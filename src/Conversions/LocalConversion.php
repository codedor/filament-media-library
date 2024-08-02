<?php

namespace Codedor\MediaLibrary\Conversions;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\WebP;
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

        if (!empty($format->definition()->toArray()['format'])) {
            $savePath = Str::replaceLast(
                $attachment->extension,
                $format->definition()->toArray()['format'],
                $savePath
            );
        }

        if (
            $force ||
            ! $attachment->getStorage()->exists("$attachment->directory/$formatName")
        ) {
            $image = Image::load($attachment->absolute_file_path);

            $format->definition()->apply($image);

            $image->save($savePath);
        }

        if (
            WebP::isEnabled() && (
                $force ||
                ! $attachment->getStorage()->exists(WebP::path($savePath, $attachment->extension))
            )
        ) {
            $image = Image::load($attachment->absolute_file_path);

            $format->definition()->apply($image);

            $image->save(WebP::path($savePath, $attachment->extension));
        }

        return true;
    }
}
