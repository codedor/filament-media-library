<?php

namespace Codedor\MediaLibrary\Filament\Actions\Traits;

use Closure;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\TranslatableTabs\Forms\TranslatableTabs;
use Codedor\TranslatableTabs\Resources\Traits\HasTranslations;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait CanUploadAttachment
{
    use HasTranslations;

    protected bool|Closure $multiple = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-media-library::upload.upload attachment'));

        $this->steps([
            $this->getUploadStep(),
            $this->getAttachmentInformationStep(),
        ]);

        $this->action(function (array $data, Get $get, Set $set, Component $component) {
            $attachmentIds = collect($data['attachments'] ?? [])
                ->map(function (string $attachmentId) use ($data) {
                    $attachment = Attachment::find($attachmentId);

                    if (! $attachment) {
                        return null;
                    }

                    $meta = $data['meta'][$attachment->md5] ?? [];

                    if (! $meta) {
                        return $attachmentId;
                    }

                    $attachment->update($this->mutateFormDataBeforeSave($meta));

                    if (array_key_exists('tags', $meta)) {
                        $attachment->tags()->sync($meta['tags']);
                    }

                    return $attachment->id;
                })
                ->filter();

            Notification::make()
                ->title(__('filament-media-library::upload.upload successful'))
                ->success()
                ->send();

            // Set the state if this is a field
            if ($this instanceof \Filament\Forms\Components\Actions\Action) {
                $set(
                    $component->getStatePath(false),
                    $this->isMultiple()
                        ? collect($component->getState())->concat($attachmentIds)->toArray()
                        : $attachmentIds->first()
                );
            }
        });
    }

    public function multiple(bool|Closure $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->multiple);
    }

    protected function getUploadStep(): Step
    {
        return Step::make(__('filament-media-library::upload.upload step title'))
            ->description(__('filament-media-library::upload.upload step intro'))
            ->schema([
                FileUpload::make('attachments')
                    ->live()
                    ->hiddenLabel()
                    ->required()
                    ->multiple(fn () => $this->isMultiple())
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                        $attachment = $file->save();

                        return $attachment->id;
                    }),
            ]);
    }

    protected function getAttachmentInformationStep(): Step
    {

        return Step::make(__('filament-media-library::upload.attachment information step title'))
            ->description(__('filament-media-library::upload.attachment information step intro'))
            ->schema(function ($state) {
                return collect($state['attachments'] ?? [])
                    ->filter(fn ($upload) => $upload instanceof TemporaryUploadedFile)
                    ->map(function ($upload) {
                        $md5 = md5_file($upload->getRealPath());

                        return Section::make($upload->getClientOriginalName())
                            ->collapsible()
                            ->collapsed()
                            ->columns()
                            ->schema([
                                TranslatableTabs::make()
                                    ->statePath("meta.{$md5}")
                                    ->icon('heroicon-o-signal')
                                    ->columnSpan(['lg' => 2])
                                    ->persistInQueryString(false)
                                    ->defaultFields([
                                        Placeholder::make('name')
                                            ->content(fn () => $upload->getClientOriginalName()),

                                        Select::make('tags')
                                            ->label(__('filament-media-library::upload.select tags'))
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
                    })
                    ->flatten()
                    ->toArray();
            });
    }
}
