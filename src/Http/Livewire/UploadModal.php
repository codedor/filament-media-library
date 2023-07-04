<?php

namespace Codedor\MediaLibrary\Http\Livewire;

use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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

    public $meta = [];

    public string $statePath = '';

    public bool $multiple = false;

    protected bool $isCachingForms = false;

    protected bool $firstCollapsable = true;

    protected $listeners = ['filament-media-library::refresh-upload-modal' => '$refresh'];

    public function mount(string $statePath = '', bool $multiple = true)
    {
        $this->multiple = $multiple;
        $this->statePath = $statePath;
        $this->form->fill();
    }

    public function submit(): void
    {
        $attachments = collect($this->form->getState()['meta'])->map(function ($data, $md5) {
            /** @var Attachment $attachment */
            $attachment = Attachment::query()
                ->where('md5', $md5)
                ->first();

            if (! $attachment) {
                return;
            }

            $attachment->update([
                'translated_name' => $data['filename'],
                'alt' => $data['alt'],
                'caption' => $data['caption'],
            ]);

            $attachment->tags()->sync($data['tags']);

            return $attachment;
        });

        Notification::make()
            ->title(__('filament_media.uploaded'))
            ->success()
            ->send();

        $this->emit('filament-media-library::update-library');

        $this->dispatchBrowserEvent('filament-media-library::uploaded-images', [
            'statePath' => $this->statePath,
            'attachments' => $attachments->pluck('id'),
        ]);

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'filament-media-library::upload-attachment-modal' . $this->statePath,
        ]);

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'filament-media-library::edit-attachment-modal',
        ]);

        $this->form->fill();
    }

    public function render()
    {
        return view('filament-media-library::livewire.upload-modal');
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
        $fileuploadField = FileUpload::make('attachments')
            ->reactive()
            ->disableLabel()
            ->required()
            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): Attachment {
                return $file->save();
            });

        if ($this->multiple) {
            $fileuploadField = $fileuploadField->multiple();
        }

        return Wizard\Step::make(__('filament_media.upload step title'))
            ->description(__('filament_media.upload step intro'))
            ->schema([
                $fileuploadField,
            ]);
    }

    protected function getAttachmentInformationStep(): Wizard\Step
    {
        $collapsableTabs = collect($this->attachments)
            ->map(function (TemporaryUploadedFile $upload) {
                $md5 = md5_file($upload->getRealPath());

                $this->meta[$md5] = [
                    'filename' => $this->meta[$md5]['filename'] ?? Str::replace(
                        ".{$upload->getClientOriginalExtension()}",
                        '',
                        $upload->getClientOriginalName()
                    ),
                    'alt' => $this->meta[$md5]['alt'] ?? null,
                    'caption' => $this->meta[$md5]['caption'] ?? null,
                    'tags' => $this->meta[$md5]['tags'] ?? null,
                ];

                $section = Section::make($upload->getClientOriginalName())
                    ->schema([
                        TextInput::make("meta.$md5.filename")
                            ->suffix('.' . $upload->getClientOriginalExtension())
                            ->dehydrateStateUsing(fn ($state) => Str::slug($state))
                            ->reactive(),

                        TextInput::make("meta.$md5.alt")
                            ->reactive(),

                        TextInput::make("meta.$md5.caption")
                            ->reactive(),

                        Select::make("meta.$md5.tags")
                            ->label(__('filament_media.tags'))
                            ->options(AttachmentTag::limit(50)->pluck('title', 'id'))
                            ->searchable()
                            ->reactive()
                            ->multiple(),
                    ])
                    ->collapsible()
                    ->collapsed(! $this->firstCollapsable)
                    ->columns();

                $this->firstCollapsable = false;

                return $section;
            });

        return Wizard\Step::make(__('filament_media.attachment information step title'))
            ->description(__('filament_media.attachment information step intro'))
            ->schema($collapsableTabs->flatten()->toArray());
    }
}
