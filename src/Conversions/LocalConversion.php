<?php

namespace Codedor\Attachments\Conversions;

use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Models\Attachment;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class LocalConversion implements Conversion
{
    public function convert(Attachment $attachment, Format $format)
    {
        $formatPrefix = $format->prefix();
        $savePath = $attachment->absolute_directory_path . '/' . $formatPrefix . $attachment->file_name;


        if (array_key_exists('format', $format->definition()->toArray()[0])) {
            $savePath = Str::replaceLast(
                $attachment->extension,
                $format->definition()->toArray()[0]['format'],
                $savePath
            );
        }

        Image::load($attachment->absolute_file_path)
            ->manipulate($format->definition())
            ->save($savePath);
    }
}
