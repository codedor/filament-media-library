<?php

namespace Codedor\Attachments\Pages;

use Codedor\Attachments\Models\Attachment;
use Codedor\Attachments\Models\AttachmentTag;
use Codedor\Attachments\Resources\AttachmentTagResource;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;
use Filament\Pages\Page;
use Filament\Resources\Form;

class Library extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';
    protected static string $view = 'laravel-attachments::pages.library';
    protected $listeners = ['laravel-attachment::update-library' => '$refresh'];

    protected static function getNavigationLabel(): string
    {
        return __('attachment.dashboard navigation title');
    }

    protected function getViewData(): array
    {
        return [
            'attachments' => Attachment::query()->paginate(18),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('openUploadModal')
                ->label(__('attachment.upload attachment'))
                ->action(fn() => $this->dispatchBrowserEvent('open-modal', [
                    'id' => 'laravel-attachment::upload-attachment-modal',
                ])),

            CreateAction::make()
                ->authorize(AttachmentTagResource::canCreate())
                ->model(AttachmentTag::class)
                ->form(
                    AttachmentTagResource::form(Form::make())
                        ->columns(2)
                        ->getSchema()
                ),
        ];
    }

    protected function getTitle(): string
    {
        return __('attachment.dashboard title');
    }
}
