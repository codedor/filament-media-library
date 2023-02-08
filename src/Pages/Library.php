<?php

namespace Codedor\Attachments\Pages;

use Codedor\Attachments\Models\Attachment;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;

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
                ->label(__('attachment.open upload modal'))
                ->action(fn() => $this->dispatchBrowserEvent('open-modal', [
                    'id' => 'laravel-attachment::upload-modal',
                ])),
        ];
    }

    protected function getTitle(): string
    {
        return __('attachment.dashboard title');
    }
}
