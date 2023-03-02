<?php

namespace Codedor\Attachments\Jobs;

use Codedor\Attachments\Formats\Format;
use Codedor\Attachments\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Image\Image;

class GenerateAttachmentFormats implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Attachment $attachment,
        protected Format $format
    ) {
    }

    public function handle()
    {
        $manipulator = Image::load($this->attachment->absolute_file_path);

        dd($manipulator);
    }
}
