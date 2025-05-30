<?php

namespace Codedor\MediaLibrary\Filament\Actions\Forms;

use Codedor\MediaLibrary\Filament\Actions\Traits\CanUploadAttachment;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Forms\Components\Actions\Action;

class UploadAttachmentAction extends \Filament\Actions\Action
{
    use CanUploadAttachment;

    public function getModel(): ?string
    {
        return Attachment::class;
    }
}
