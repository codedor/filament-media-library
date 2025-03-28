<?php

namespace Codedor\MediaLibrary\Conversions;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Support\TemporaryDirectory;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class S3Conversion implements Conversion
{
    public function convert(Attachment $attachment, Format $format, bool $force = false): bool
    {
        if (! is_convertible_image($attachment->extension)) {
            return false;
        }

        $formatName = $format->filename($attachment);
        $savePath = $attachment->directory . '/' . $formatName;

        if (! empty($format->definition()->toArray()['format'])) {
            $savePath = Str::replaceLast(
                $attachment->extension,
                $format->definition()->toArray()['format'],
                $savePath
            );
        }

        $formatPath = "$attachment->directory/$formatName";

        if (
            $force ||
            ! $attachment->getStorage()->exists($formatPath)
        ) {
            $temporaryDirectory = TemporaryDirectory::create();
            $tempPath = $temporaryDirectory->path(Str::random(16) . '.' . $attachment->extension);
            $disk = $attachment->getStorage();

            file_put_contents($tempPath, $disk->readStream($attachment->file_path));

            $image = Image::load($tempPath);

            $format->definition()->apply($image);

            $image->save($tempPath);

            $file = fopen($tempPath, 'r');

            $disk->put(
                $formatPath,
                $file,
            );

            if (is_resource($file)) {
                fclose($file);
            }

            $temporaryDirectory->delete();
        }

        return true;
    }
}
