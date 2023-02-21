<?php

namespace Codedor\Attachments\Http\Livewire;

use Codedor\Attachments\Models\Attachment;
use Codedor\Attachments\Models\AttachmentTag;
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

    protected bool $isCachingForms = false;

    protected bool $firstCollabsible = true;

    protected $listeners = ['laravel-attachment::refresh-upload-modal' => '$refresh'];

    public function mount()
    {
        $this->form->fill();
    }

    public function submit(): void
    {
        collect($this->meta)
            ->each(function ($data, $md5) {
                /** @var Attachment $attachment */
                $attachment = Attachment::query()
                    ->where('md5', $md5)
                    ->first();

                if (! $attachment) {
                    return;
                }

                $attachment->update([
                    'translated_name' => json_encode($data['filename']),
                    'alt' => json_encode($data['alt']),
                    'caption' => json_encode($data['caption']),
                ]);

                $attachment->tags()->sync($data['tags']);
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
                    }),
            ]);
    }

    protected function getAttachmentInformationStep(): Wizard\Step
    {
        $collapsibles = collect($this->attachments)
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
                            ->label(__('laravel-attachment::tags'))
                            ->options(AttachmentTag::limit(50)->pluck('title', 'id'))
                            ->searchable()
                            ->reactive()
                            ->multiple(),
                    ])
                    ->collapsible()
                    ->collapsed(! $this->firstCollabsible)
                    ->columns();

                $this->firstCollabsible = false;

                return $section;
            });

        return Wizard\Step::make(__('laravel-attachment::attachment information step title'))
            ->description(__('laravel-attachment::attachment information step intro'))
            ->schema($collapsibles->flatten()->toArray());
    }
}
