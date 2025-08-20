<?php

namespace Codedor\MediaLibrary\Filament\Actions\Traits;

use Closure;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\TranslatableTabs\Forms\TranslatableTabs;
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
use Illuminate\Support\Arr;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait CanUploadAttachment
{
    protected bool|Closure $multiple = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-media-library::upload.upload attachment'));

        $this->steps([
            $this->getUploadStep(),
            $this->getAttachmentInformationStep(),
        ]);

        $this->action(function (array $data, Set $set, Component $component) {
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

                    $attachment->update($this->mutateData($meta));

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
        })->closeModalByClickingAway(false);
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
            ->afterValidation(function (Get $get, Set $set) {
                foreach (Arr::wrap($get('attachments')) as $file) {
                    if ($file instanceof TemporaryUploadedFile) {
                        $md5 = md5($file->hashName());

                        $set("meta.{$md5}.name", '');
                        $set("meta.{$md5}.tags", []);
                    }
                }
            })
            ->schema([
                FileUpload::make('attachments')
                    ->live()
                    ->hiddenLabel()
                    ->required()
                    ->multiple(fn () => $this->isMultiple())
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, Set $set): string {
                        $attachment = $file->save();

                        return $attachment->id;
                    }),
            ]);
    }

    protected function getAttachmentInformationStep(): Step
    {
        return Step::make(__('filament-media-library::upload.attachment information step title'))
            ->description(__('filament-media-library::upload.attachment information step intro'))
            ->schema(function ($state, Get $get) {
                return collect($state['attachments'] ?? [])
                    ->filter(fn ($upload) => $upload instanceof TemporaryUploadedFile)
                    ->map(function ($upload) use ($get) {
                        $md5 = md5($upload->hashName());

                        $defaultFields = [
                            Placeholder::make('name')
                                ->label(__('filament-media-library::upload.name'))
                                ->content(fn () => $upload->getClientOriginalName()),
                        ];

                        if (! is_null($get("meta.{$md5}.tags"))) {
                            $defaultFields[] = Select::make('tags')
                                ->label(__('filament-media-library::upload.select tags'))
                                ->multiple()
                                ->default([])
                                ->options(AttachmentTag::all()->pluck('title', 'id')->toArray());
                        }

                        return Section::make($upload->getClientOriginalName())
                            ->collapsible()
                            ->columns()
                            ->schema([
                                TranslatableTabs::make()
                                    ->statePath("meta.{$md5}")
                                    ->icon('heroicon-o-signal')
                                    ->columnSpan(['lg' => 2])
                                    ->persistInQueryString(false)
                                    ->defaultFields($defaultFields)
                                    ->translatableFields(fn () => [
                                        // TextInput::make('translated_name')
                                        //     ->suffix('.' . $upload->getClientOriginalExtension())
                                        //     ->dehydrateStateUsing(fn ($state) => Str::slug($state)),

                                        TextInput::make('alt')
                                            ->label(__('filament-media-library::upload.alt text')),

                                        TextInput::make('caption')
                                            ->label(__('filament-media-library::upload.caption')),
                                    ]),
                            ]);
                    })
                    ->flatten()
                    ->toArray();
            });
    }

    protected function mutateData(array $data): array
    {
        $model = app($this->getModel());
        foreach (Arr::except($data, $model->getFillable()) as $locale => $values) {
            if (! is_array($values)) {
                continue;
            }

            foreach (Arr::only($values, $model->getTranslatableAttributes()) as $key => $value) {
                $data[$key][$locale] = $value;
            }
        }

        return $data;
    }
}
