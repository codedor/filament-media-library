<?php

namespace Codedor\MediaLibrary\Filament\Actions;

use Codedor\MediaLibrary\Actions\AttachmentActions;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class DeleteAttachmentBulkAction extends DeleteBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalContent(function (Collection $records) {
                $failedRecords = $records
                    ->mapWithKeys(fn ($record) => [
                        $record->getKey() => AttachmentActions::findRelatedRecords($record),
                    ])
                    ->filter(fn ($relatedRecords) => $relatedRecords->isNotEmpty());

                if ($failedRecords->isEmpty()) {
                    return null;
                }

                return new HtmlString(
                    '<div class="text-sm">' .
                    '<p class="font-semibold text-danger-600 dark:text-danger-400">' .
                    __('filament-media-library::attachment.delete failed description') .
                    '</p><br>' .
                    AttachmentActions::formatFailedRecords($failedRecords) .
                    '</div>'
                );
            })
            ->modalSubmitAction(function (Collection $records) {
                $hasRelatedRecords = $records->contains(
                    fn ($record) => AttachmentActions::findRelatedRecords($record)->isNotEmpty()
                );

                return $hasRelatedRecords ? false : null;
            });
    }
}
