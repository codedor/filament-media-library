<?php

namespace Codedor\Attachments\Pages;

use Codedor\Attachments\Models\Attachment;
use Codedor\Attachments\Models\AttachmentTag;
use Codedor\Attachments\Resources\AttachmentTagResource;
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

    protected static string $view = 'laravel-attachments::pages.library';
    public $search = '';
    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
    ];
    protected int $perPage = 18;
    protected $listeners = ['laravel-attachment::update-library' => '$refresh'];

    protected static function getNavigationLabel(): string
    {
        return __('attachment.dashboard navigation title');
    }

    public function deleteAttachment($id)
    {
        $attachment = Attachment::find($id);

        if (! $attachment) {
            return;
        }

        $attachment->delete();

        Notification::make("laravel-attachment::attachment-deleted-$attachment->id")
            ->body(__('laravel-attachment.deleted attachment :name', [
                'name' => $attachment->name,
            ]))
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
            'attachments' => Attachment::query()
                ->search($this->search)
                ->paginate($this->perPage),
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
