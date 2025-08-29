<?php

namespace Codedor\MediaLibrary\Filament\Actions\Tables;

use Codedor\MediaLibrary\Filament\Actions\Traits\CanUploadAttachment;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Actions\Action;

class TableUploadAttachmentAction extends Action
{
    use CanUploadAttachment;

    public function getModel(bool $withDefault = true): ?string
    {
        return Attachment::class;
    }
}
