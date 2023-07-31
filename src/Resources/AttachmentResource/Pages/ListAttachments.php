<?php

namespace Codedor\MediaLibrary\Resources\AttachmentResource\Pages;

use Codedor\MediaLibrary\Filament\Actions\UploadAttachmentAction;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\MediaLibrary\Resources\AttachmentResource;
use Codedor\MediaLibrary\Resources\AttachmentTagResource;
use Filament\Pages\Actions;
use Filament\Forms\Form;
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
                    AttachmentTagResource::form(Form::make($this))
                        ->columns(2)
                        ->getComponents()
                ),

            Actions\Action::make('upload')
                ->label(__('filament_media.upload attachment'))
                ->action(fn () => $this->dispatch(
                    'open-modal',
                    id: 'filament-media-library::upload-attachment-modal',
                )),
//
//            UploadAttachmentAction::make('test'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [12, 24, 48, 96];
    }
}
