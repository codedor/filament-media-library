<?php

namespace Codedor\MediaLibrary\Filament\Notifications;

use Codedor\MediaLibrary\Exceptions\DeleteFailedException;
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
            ->warning()
            ->body(fn () => static::formatFailedRecords($this->exception->getFailedRecords() ?? []));
    }

    protected static function formatFailedRecords(array $failedRecords): string
    {
        return collect($failedRecords)
            ->map(function ($relatedRecords) {
                return collect($relatedRecords)
                    ->map(function ($record) {
                        $resource = class_basename($record);
                        $url = get_resource_url_by_model($record, 'edit');
                        $title = $record->working_title;

                        return static::formatRecordLink($resource, $url, $title);
                    })
                    ->implode('');
            })
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
