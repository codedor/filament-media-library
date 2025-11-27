<?php

namespace Codedor\MediaLibrary\Filament\Notifications;

use Codedor\MediaLibrary\Actions\AttachmentActions;
use Codedor\MediaLibrary\Exceptions\DeleteFailedException;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class FailedDeletionNotification extends Notification
{
    protected DeleteFailedException $exception;

    public function exception(DeleteFailedException $exception): static
    {
        $this->exception = $exception;

        return $this;
    }

    protected function setUp(): void
    {
        $this
            ->title(__('filament-media-library::attachment.delete failed'))
            ->danger()
            ->body(fn () => __('filament-media-library::attachment.delete failed description') . '<br>' .
                AttachmentActions::formatFailedRecords($this->exception->getFailedRecords())
            )
            ->persistent()
            ->actions([
                Action::make('close')
                    ->button()
                    ->close(),
            ]);
    }
}
