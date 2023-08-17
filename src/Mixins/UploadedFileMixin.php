<?php

namespace Codedor\MediaLibrary\Mixins;

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Thumbnail;
use Codedor\MediaLibrary\Jobs\GenerateAttachmentFormat;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UploadedFileMixin
{
    public function save()
    {
        return function (string $disk = 'public') {
            $fileType = $this->fileType();

            if ($fileType === 'image') {
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
                'name' => Str::replace(
                    ".{$this->getClientOriginalExtension()}",
                    '',
                    $this->getClientOriginalName()
                ),
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
                Formats::findByKey(Thumbnail::class),
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
}
