<?php

namespace Wotz\MediaLibrary\Resources\AttachmentResource\Pages;

use Wotz\MediaLibrary\Resources\AttachmentResource;
use Wotz\TranslatableTabs\Resources\Traits\HasTranslations;
use Filament\Resources\Pages\EditRecord;

class EditAttachment extends EditRecord
{
    use HasTranslations;

    protected static string $resource = AttachmentResource::class;

    protected function getActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
