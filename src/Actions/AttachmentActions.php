<?php

namespace Codedor\MediaLibrary\Actions;

use Codedor\MediaLibrary\Exceptions\DeleteFailedException;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AttachmentActions
{
    public static function delete($records): void
    {
        $records = $records instanceof Collection ? $records : collect([$records]);
        $failedRecords = collect();

        foreach ($records as $record) {
            $relatedRecords = self::findRelatedRecords($record);

            if ($relatedRecords->isNotEmpty()) {
                $failedRecords->put($record->getKey(), $relatedRecords);
            } else {
                $record->delete();
            }
        }

        if ($failedRecords->isNotEmpty()) {
            throw new DeleteFailedException($failedRecords);
        }
    }

    public static function findRelatedRecords(Attachment $record): Collection
    {
        $records = collect();
        Formats::getRegisteredModelsWithFields()->each(function ($model) use ($record, &$records) {
            $modelInstance = new $model['model'];
            $table = $modelInstance->getTable();
            $columns = Schema::getColumnListing($table);

            $model['fields']
                ->filter(fn ($field) => in_array($field, $columns))
                ->each(function ($field) use ($modelInstance, $record, &$records) {
                    $modelInstance->where($field, $record->id)
                        ->get()
                        ->each(fn ($record1) => $records->push($record1));
                });
        });

        return $records;
    }

    public static function formatFailedRecords(Collection $failedRecords): string
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
