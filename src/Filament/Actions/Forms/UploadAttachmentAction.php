<?php

namespace Codedor\MediaLibrary\Filament\Actions\Forms;

use Codedor\MediaLibrary\Filament\Actions\Traits\CanUploadAttachment;
use Codedor\MediaLibrary\Models\Attachment;

class UploadAttachmentAction extends \Filament\Actions\Action
{
    use CanUploadAttachment;

    public function getModel(): ?string
    {
        return Attachment::class;
    }
}
