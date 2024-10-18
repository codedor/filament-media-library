<?php

namespace Codedor\MediaLibrary\Filament\Notifications;

use Filament\Notifications\Notification;

class FailedDeletionNotification extends Notification
{
    protected $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public static function make($exception): static
    {
        return (new static($exception))
            ->title(__('filament-media-library::attachment.delete failed'))
            ->warning()
            ->body(fn () => static::formatFailedRecords($exception->getFailedRecords()));
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
