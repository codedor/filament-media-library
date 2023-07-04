<?php

namespace Codedor\MediaLibrary\Resources\AttachmentTagResource\Pages;

use Codedor\MediaLibrary\Resources\AttachmentTagResource;
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
