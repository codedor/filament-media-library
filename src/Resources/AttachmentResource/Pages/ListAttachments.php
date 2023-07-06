<?php

namespace Codedor\MediaLibrary\Resources\AttachmentResource\Pages;

use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\MediaLibrary\Resources\AttachmentResource;
use Codedor\MediaLibrary\Resources\AttachmentTagResource;
use Filament\Pages\Actions;
use Filament\Resources\Form;
use Filament\Resources\Pages\ListRecords;

class ListAttachments extends ListRecords
{
    protected static string $resource = AttachmentResource::class;

    protected $listeners = [
        'filament-media-library::update-library' => '$refresh',
    ];

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('filament_media.create new attachment tag'))
                ->authorize(AttachmentTagResource::canCreate())
                ->model(AttachmentTag::class)
                ->outlined()
                ->form(
                    AttachmentTagResource::form(Form::make())
                        ->columns(2)
                        ->getSchema()
                ),

            Actions\Action::make('upload')
                ->label(__('filament_media.upload attachment'))
                ->action(fn () => $this->dispatchBrowserEvent('open-modal', [
                    'id' => 'filament-media-library::upload-attachment-modal',
                ])),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [12, 24, 48, 96];
    }
}
