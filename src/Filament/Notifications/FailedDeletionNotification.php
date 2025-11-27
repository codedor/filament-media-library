<?php

namespace Codedor\MediaLibrary\Filament\Notifications;

use Codedor\MediaLibrary\Exceptions\DeleteFailedException;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

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
            ->warning()
            ->body(fn () => static::formatFailedRecords($this->exception->getFailedRecords()));
    }

    protected static function formatFailedRecords(Collection $failedRecords): string
    {
        return $failedRecords
            ->map(fn ($relatedRecords) => collect($relatedRecords)
                ->map(fn ($record) => static::formatRecordLink(
                    class_basename($record),
                    ($resource = Filament::getModelResource($record))
                        ? $resource::getUrl('edit', ['record' => $record])
                        : null,
                    $record->working_title
                ))
                ->implode(''))
            ->implode('<br>');
    }

    protected static function formatRecordLink(string $resource, ?string $url, string $title): string
    {
        if (! $url) {
            return "- <strong>{$resource}:</strong> {$title}<br>";
        }

        return "- <strong>{$resource}:</strong> <a href='{$url}'><u>{$title}</u></a><br>";
    }
}
