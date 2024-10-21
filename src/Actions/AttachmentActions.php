<?php

namespace Codedor\MediaLibrary\Actions;

use Codedor\MediaLibrary\Exceptions\DeleteFailedException;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\Attachment;
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

    private static function findRelatedRecords(Attachment $record): Collection
    {
        $records = collect();
        Formats::getRegisteredModelsWithFields()->each(function ($model) use ($record, &$records) {
            $modelInstance = new $model->model;
            $table = $modelInstance->getTable();
            $columns = Schema::getColumnListing($table);

            $model->fields->each(function ($field) use ($modelInstance, $record, $columns, &$records) {
                if (! in_array($field, $columns)) {
                    return;
                }

                $modelInstance->where($field, $record->id)
                    ->get()
                    ->each(fn ($record1) => $records->push($record1));
            });
        });

        return $records;
    }
}
