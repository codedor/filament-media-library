<?php

namespace Codedor\Attachments\Pages;

use Filament\Pages\Actions\Action;
use Filament\Pages\Page;

class Library extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    protected static string $view = 'laravel-attachments::pages.library';

    protected static function getNavigationLabel(): string
    {
        return __('attachment.dashboard navigation title');
    }

    protected function getActions(): array
    {
        return [
            Action::make('openUploadModal')
                ->label(__('attachment.open upload modal'))
                ->action(fn() => $this->dispatchBrowserEvent('open-modal', [
                    'id' => 'attachment::upload-modal',
                ])),
        ];
    }

    protected function getTitle(): string
    {
        return __('attachment.dashboard title');
    }
}
