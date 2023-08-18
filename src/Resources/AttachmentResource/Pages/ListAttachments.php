<?php

namespace Codedor\MediaLibrary\Resources\AttachmentResource\Pages;

use Codedor\MediaLibrary\Filament\Actions\Tables\TableUploadAttachmentAction;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\MediaLibrary\Resources\AttachmentResource;
use Codedor\MediaLibrary\Resources\AttachmentTagResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListAttachments extends ListRecords
{
    protected static string $resource = AttachmentResource::class;

    protected $listeners = [
        'filament-media-library::update-library' => '$refresh',
    ];

    public function getTitle(): string|Htmlable
    {
        return __('filament-media-library::attachment.dashboard navigation title');
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('filament-media-library::tags.create new attachment tag'))
                ->authorize(AttachmentTagResource::canCreate())
                ->model(AttachmentTag::class)
                ->outlined()
                ->form(
                    AttachmentTagResource::form(Form::make($this))
                        ->columns(2)
                        ->getComponents()
                ),

            TableUploadAttachmentAction::make('upload')
                ->multiple(),
        ];
    }
}
