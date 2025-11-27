<?php

namespace Codedor\MediaLibrary\Filament\Actions;

use Codedor\MediaLibrary\Actions\AttachmentActions;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\HtmlString;

class DeleteAttachmentAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalContent(function (Attachment $record) {
                $relatedRecords = AttachmentActions::findRelatedRecords($record);

                if ($relatedRecords->isEmpty()) {
                    return null;
                }

                return new HtmlString(
                    '<div class="text-sm">' .
                    '<p class="font-semibold text-danger-600 dark:text-danger-400">' .
                    __('filament-media-library::attachment.delete failed description') .
                    '</p><br>' .
                    AttachmentActions::formatFailedRecords(
                        collect([$record->getKey() => $relatedRecords])
                    ) .
                    '</div>'
                );
            })
            ->modalSubmitAction(fn (Attachment $record) =>
                AttachmentActions::findRelatedRecords($record)->isNotEmpty()
                    ? false
                    : null
            );
    }
}
