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

                $description = __('filament-media-library::attachment.delete failed description');
                $formattedRecords = AttachmentActions::formatFailedRecords(
                    collect([$record->getKey() => $relatedRecords])
                );

                return new HtmlString(<<<HTML
                    <div class="rounded-lg bg-danger-50 dark:bg-danger-500/10 p-4">
                        <p class="text-sm font-medium text-danger-600 dark:text-danger-400 mb-3">
                            {$description}
                        </p>
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            {$formattedRecords}
                        </div>
                    </div>
                    HTML
                );
            })
            ->modalSubmitAction(fn (Attachment $record) => AttachmentActions::findRelatedRecords($record)->isNotEmpty()
                    ? false
                    : null
            );
    }
}
