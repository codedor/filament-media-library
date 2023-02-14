<?php

namespace Codedor\Attachments\Http\Livewire;

use Closure;
use Codedor\Attachments\Models\Attachment;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;

class UploadModal extends Component implements HasForms
{
    use InteractsWithForms;

    public $attachments = [];
    public $attachmentMetaData = [];

    protected bool $firstCollabsible = true;

    protected $listeners = ['laravel-attachment::refresh-upload-modal' => '$refresh'];

    public function mount()
    {
        $this->form->fill();
    }

    public function submit(): void
    {
        collect($this->form->getState()['attachmentMetaData'] ?? [])
            ->each(function ($data, $md5) {
                Attachment::query()->where('md5', $md5)->update([
                    'translated_name' => json_encode($data['filename']),
                    'alt' => json_encode($data['alt']),
                    'caption' => json_encode($data['caption']),
                ]);
            });

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'laravel-attachment::upload-attachment-modal',
        ]);

        Notification::make()
            ->title(__('attachment.uploaded'))
            ->success()
            ->send();

        $this->emit('laravel-attachment::update-library');

        $this->form->fill();
    }

    public function render()
    {
        return view('laravel-attachments::livewire.upload-modal');
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make()
                ->schema([
                    $this->getUploadStep(),
                    $this->getAttachmentInformationStep(),
                ]),
        ];
    }

    protected function getUploadStep(): Wizard\Step
    {
        return Wizard\Step::make(__('laravel-attachment::upload step title'))
            ->description(__('laravel-attachment::upload step intro'))
            ->schema([
                FileUpload::make('attachments')
                    ->reactive()
                    ->disableLabel()
                    ->required()
                    ->multiple()
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): Attachment {
                        return $file->save();
                    })
                    ->afterStateUpdated(function (Closure $get, Closure $set, $state) {
                        if (filled($state)) {
                            collect($state)
                                ->each(function (TemporaryUploadedFile $file) use ($set) {
                                    $md5 = md5_file($file->getRealPath());
                                    $set(
                                        "attachmentMetaData.$md5.filename",
                                        Str::replace(
                                            ".{$file->getClientOriginalExtension()}",
                                            '',
                                            $file->getClientOriginalName()
                                        )
                                    );
                                });
                        }
                    }),
            ]);
    }

    protected function getAttachmentInformationStep(): Wizard\Step
    {
        $collapsibles = collect($this->attachments)
            ->map(function (TemporaryUploadedFile $upload) {
                $md5 = md5_file($upload->getRealPath());

                $section = Section::make($upload->getClientOriginalName())
                    ->schema([
                        TextInput::make("attachmentMetaData.$md5.filename")
                            ->suffix('.' . $upload->getClientOriginalExtension())
                            ->dehydrateStateUsing(fn($state) => Str::slug($state)),
                        TextInput::make("attachmentMetaData.$md5.alt"),
                        TextInput::make("attachmentMetaData.$md5.caption"),
                    ])
                    ->collapsible()
                    ->collapsed($this->firstCollabsible ? false : true)
                    ->columns();

                $this->firstCollabsible = false;

                return $section;
            });

        return Wizard\Step::make(__('laravel-attachment::attachment information step title'))
            ->description(__('laravel-attachment::attachment information step intro'))
            ->schema($collapsibles->flatten()->toArray());
    }
}
