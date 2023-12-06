<?php

namespace Codedor\MediaLibrary\Mixins;

use Codedor\MediaLibrary\Exceptions\CantOpenFileFromUrlException;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Thumbnail;
use Codedor\MediaLibrary\Jobs\GenerateAttachmentFormat;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @mixin UploadedFile
 */
class UploadedFileMixin
{
    public function save()
    {
        return function (string $disk = 'public') {
            $fileType = $this->fileType();
            $extension = $this->getClientOriginalExtension();

            if (is_image_with_dimensions($extension)) {
                $dimensions = $this->dimensions();
            } else {
                $dimensions = [];
            }

            $data = [
                'extension' => $this->getClientOriginalExtension(),
                'mime_type' => $this->getMimeType(),
                'md5' => $this->getMd5(),
                'type' => $fileType,
                'size' => $this->getSize(),
                'width' => $dimensions[0] ?? null,
                'height' => $dimensions[1] ?? null,
                'disk' => $disk,
                'name' => Str::of($this->getClientOriginalName())
                    ->replace(".{$this->getClientOriginalExtension()}", '')
                    ->slug(),
            ];

            /** @var \Codedor\MediaLibrary\Models\Attachment $attachment */
            $attachment = Attachment::firstOrCreate([
                'md5' => $data['md5'],
            ], $data);

            Storage::disk($disk)->putFileAs(
                $attachment->directory,
                $this,
                $attachment->filename
            );

            Formats::dispatchGeneration($attachment);

            // Create the thumbnail now, so we don't have an empty preview in the next response
            GenerateAttachmentFormat::dispatchAfterResponse(
                $attachment,
                Thumbnail::make(),
            );

            return $attachment;
        };
    }

    public function isImage()
    {
        return function () {
            return Validator::make(['data' => $this], ['data' => 'image'])->passes();
        };
    }

    public function getMd5()
    {
        return function () {
            $path = $this->getRealPath();

            return md5_file($path);
        };
    }

    public function fileType()
    {
        return function (): string {
            foreach (config('filament-media-library.extensions', []) as $type => $extensions) {
                if (in_array($this->getClientOriginalExtension(), $extensions)) {
                    return $type;
                }
            }

            return 'other';
        };
    }

    public static function createFromUrl()
    {
        return static function (
            string $url,
            string $originalName = '',
            ?string $mimeType = null,
            ?int $error = null,
            bool $test = false
        ): self {
            if (! $stream = @fopen($url, 'r')) {
                throw new CantOpenFileFromUrlException($url);
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'url-file-');

            file_put_contents($tempFile, $stream);

            if (! $originalName) {
                $originalName = basename($url);
            }

            if (! $mimeType) {
                $mimeType = mime_content_type($tempFile);
            }

            return new static($tempFile, $originalName, $mimeType, $error, $test);
        };
    }
}
