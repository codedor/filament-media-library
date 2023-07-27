<?php

namespace Codedor\MediaLibrary\Http\Livewire;

use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\TranslatableTabs\Forms\TranslatableTabs;
use Codedor\TranslatableTabs\Resources\Traits\HasTranslations;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
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
    use HasTranslations;

    public $attachments = [];

    public $meta = [];

    public string $statePath = '';

    public bool $multiple = false;

    protected bool $isCachingForms = false;

    protected bool $firstCollapsable = true;

    protected $listeners = [
        'filament-media-library::refresh-upload-modal' => '$refresh',
        'filament-media-library::open-upload-modal' => 'openUploadModal',
    ];

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

            $attachment->update($this->mutateFormDataBeforeSave($data));
            $attachment->tags()->sync($data['tags']);

            return $attachment;
        });

        Notification::make()
            ->title(__('filament_media.uploaded'))
            ->success()
            ->send();

        $this->emit('filament-media-library::update-library');

        $this->dispatch(
            'filament-media-library::uploaded-images',
            statePath: $this->statePath,
            attachments: $attachments->pluck('id'),
        );

        $this->dispatch(
            'close-modal',
            id: 'filament-media-library::upload-attachment-modal',
        );

        $this->dispatch(
            'close-modal',
            id: 'filament-media-library::edit-attachment-modal',
        );

        $this->form->fill();
    }

    public function openUploadModal($data): void
    {
        $this->multiple = $data['multiple'] ?? false;
        $this->statePath = $data['statePath'] ?? '';
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
        $fileUploadField = FileUpload::make('attachments')
            ->reactive()
            ->disableLabel()
            ->required()
            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): Attachment {
                return $file->save();
            });

        if ($this->multiple) {
            $fileUploadField = $fileUploadField->multiple();
        }

        return Wizard\Step::make(__('filament_media.upload step title'))
            ->description(__('filament_media.upload step intro'))
            ->schema([
                $fileUploadField,
            ]);
    }

    protected function getAttachmentInformationStep(): Wizard\Step
    {
        $collapsableTabs = collect($this->attachments)
            ->map(function (TemporaryUploadedFile $upload) {
                $md5 = md5_file($upload->getRealPath());

                $this->meta[$md5] = [
                    'tags' => $this->meta[$md5]['tags'] ?? null,
                ];

                $section = Section::make($upload->getClientOriginalName())
                    ->collapsible()
                    ->collapsed(! $this->firstCollapsable)
                    ->columns()
                    ->schema([
                        TranslatableTabs::make('Translations')
                            ->statePath("meta.{$md5}")
                            ->icon('heroicon-o-status-online')
                            ->iconColor('success')
                            ->columnSpan(['lg' => 2])
                            ->defaultFields([
                                Placeholder::make('name')
                                    ->content(fn () => $upload->getClientOriginalName()),

                                Select::make('tags')
                                    ->label(__('filament_media.tags'))
                                    ->options(AttachmentTag::limit(50)->pluck('title', 'id'))
                                    ->searchable()
                                    ->multiple(),
                            ])
                            ->translatableFields(fn () => [
                                // TextInput::make('translated_name')
                                //     ->suffix('.' . $upload->getClientOriginalExtension())
                                //     ->dehydrateStateUsing(fn ($state) => Str::slug($state)),

                                TextInput::make('alt')
                                    ->label('Alt text'),

                                TextInput::make('caption'),
                            ]),
                    ]);

                $this->firstCollapsable = false;

                return $section;
            });

        return Wizard\Step::make(__('filament_media.attachment information step title'))
            ->description(__('filament_media.attachment information step intro'))
            ->schema($collapsableTabs->flatten()->toArray());
    }

    protected function getModel()
    {
        return Attachment::class;
    }
}
