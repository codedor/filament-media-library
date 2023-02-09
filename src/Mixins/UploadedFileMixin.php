<?php

namespace Codedor\Attachments\Mixins;

use Codedor\Attachments\Entities\Dimension;
use Codedor\Attachments\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UploadedFileMixin
{
    public function save()
    {
        return function (string $disk = 'public') {
            /** @var Dimension $dimensions */
            $dimensions = $this->dimensions();

            $data = [
                'extension' => $this->getClientOriginalExtension(),
                'mime_type' => $this->getMimeType(),
                'md5' => $this->getMd5(),
                'type' => $this->fileType(),
                'size' => $this->getSize(),
                'width' => $dimensions?->width,
                'height' => $dimensions?->height,
                'disk' => $disk,
                'name' => Str::replace(
                    ".{$this->getClientOriginalExtension()}",
                    '',
                    $this->getClientOriginalName()
                ),
            ];

            /** @var \Codedor\Attachments\Models\Attachment $attachment */
            $attachment = Attachment::firstOrCreate([
                'md5' => $data['md5'],
            ], $data);

            Storage::disk($disk)->putFileAs(
                $attachment->directory(),
                $this,
                $attachment->filename()
            );
        };
    }

    public function dimensions()
    {
        return function (): Dimension|null {
            if (
                ! $this->isImage() ||
                in_array($this->getMimeType(), ['image/svg+xml', 'image/svg'])) {
                return null;
            }

            return Dimension::for($this->path());
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
            foreach (config('laravel-attachments.extensions', []) as $type => $extensions) {
                if (in_array($this->getClientOriginalExtension(), $extensions)) {
                    return $type;
                }
            }

            return 'other';
        };
    }
}