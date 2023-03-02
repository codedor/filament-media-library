<?php

namespace Codedor\Attachments\Conversions;

use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Models\Attachment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class LocalConversion implements Conversion
{
    public function convert(Attachment $attachment, Format $format, bool $force = false)
    {
        $formatPrefix = $format->prefix();
        $formatName = $formatPrefix . $attachment->file_name;
        $savePath = $attachment->absolute_directory_path . '/' . $formatName;

        if (array_key_exists('format', $format->definition()->toArray()[0])) {
            $savePath = Str::replaceLast(
                $attachment->extension,
                $format->definition()->toArray()[0]['format'],
                $savePath
            );
        }

        if (! $force && File::exists($savePath)) {
            return;
        }

        Image::load($attachment->absolute_file_path)
            ->manipulate($format->definition())
            ->save($savePath);
    }
}
