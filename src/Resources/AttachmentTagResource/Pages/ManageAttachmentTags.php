<?php

namespace Codedor\Attachments\Resources\AttachmentTagResource\Pages;

use Codedor\Attachments\Resources\AttachmentTagResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAttachmentTags extends ManageRecords
{
    protected static string $resource = AttachmentTagResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
