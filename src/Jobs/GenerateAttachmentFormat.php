<?php

namespace Codedor\MediaLibrary\Jobs;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAttachmentFormat implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Attachment $attachment,
        public Format $format
    ) {
        //
    }

    public function handle()
    {
        $this->format->conversion()->convert(
            attachment: $this->attachment,
            format: $this->format
        );
    }
}
