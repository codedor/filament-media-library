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

    public string $statePath = '';

    protected bool $isCachingForms = false;

    protected bool $firstCollapsable = true;

    protected $listeners = ['laravel-attachment::refresh-upload-modal' => '$refresh'];

    public function mount(string $statePath = '')
    {
        $this->statePath = $statePath;
        $this->form->fill();
    }

    public function submit(): void
    {
        $this->emit('laravel-attachment::update-library');

        $attachments = collect($this->meta)->map(function ($data, $md5) {
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
            ->title(__('attachment.uploaded'))
            ->success()
            ->send();

        $this->emit('laravel-attachment::update-library');

        $this->dispatchBrowserEvent('laravel-attachment::uploaded-images', [
            'statePath' => $this->statePath,
            'attachments' => $attachments->pluck('id'),
        ]);

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'laravel-attachment::upload-attachment-modal',
        ]);

        $this->dispatchBrowserEvent('close-modal', [
            'id' => 'laravel-attachment::edit-attachment-modal',
        ]);

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
                        // TODO: code does not pass here, tmp fix in getAttachmentInformationStep()
                        return $file->save();
                    }),
            ]);
    }

    protected function getAttachmentInformationStep(): Wizard\Step
    {
        $collapsableTabs = collect($this->attachments)
            ->map(function (TemporaryUploadedFile $upload) {
                $md5 = md5_file($upload->getRealPath());

                // TODO: temp fix for above ->saveUploadedFileUsing() issue
                $upload->save();

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
                    ->collapsed(! $this->firstCollapsable)
                    ->columns();

                $this->firstCollapsable = false;

                return $section;
            });

        return Wizard\Step::make(__('laravel-attachment::attachment information step title'))
            ->description(__('laravel-attachment::attachment information step intro'))
            ->schema($collapsableTabs->flatten()->toArray());
    }
}
