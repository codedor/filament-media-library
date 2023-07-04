<?php

namespace Codedor\MediaLibrary\Pages;

use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\MediaLibrary\Resources\AttachmentTagResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\CreateAction;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Livewire\WithPagination;

class Library extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    protected static string $view = 'filament-media-library::pages.library';

    public string $search = '';

    public null|string $attachmentToDelete = null;

    protected int $perPage = 18;

    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
    ];

    protected $listeners = [
        'filament-media-library::update-library' => '$refresh',
    ];

    protected static function getNavigationLabel(): string
    {
        return __('filament_media.dashboard navigation title');
    }

    public function deleteAttachment()
    {
        Attachment::find($this->attachmentToDelete)?->delete();

        Notification::make()
            ->title(__('filament_media.deleted successfully'))
            ->success()
            ->send();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function getViewData(): array
    {
        return [
            'attachments' => Attachment::latest()
                ->search($this->search)
                ->paginate($this->perPage),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('openUploadModal')
                ->label(__('filament_media.upload attachment'))
                ->action(fn () => $this->dispatchBrowserEvent('open-modal', [
                    'id' => 'filament-media-library::upload-attachment-modal',
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
        return __('filament_media.dashboard title');
    }
}
