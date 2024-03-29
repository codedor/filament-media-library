<?php

namespace Codedor\MediaLibrary\Resources\AttachmentResource\Pages;

use Codedor\MediaLibrary\Resources\AttachmentResource;
use Codedor\TranslatableTabs\Resources\Traits\HasTranslations;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttachment extends EditRecord
{
    use HasTranslations;

    protected static string $resource = AttachmentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
