<?php

namespace Wotz\MediaLibrary\Resources\AttachmentTagResource\Pages;

use Wotz\MediaLibrary\Resources\AttachmentTagResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAttachmentTags extends ManageRecords
{
    protected static string $resource = AttachmentTagResource::class;

    protected function getActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
