<?php

namespace Codedor\MediaLibrary\Filament\Actions\Forms;

use Codedor\MediaLibrary\Filament\Actions\Traits\CanUploadAttachment;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Forms\Components\Actions\Action;

class UploadAttachmentAction extends Action
{
    use CanUploadAttachment;

    protected function getModel(): null|string
    {
        return Attachment::class;
    }
}
