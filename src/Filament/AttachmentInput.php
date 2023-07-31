<?php

namespace Codedor\MediaLibrary\Filament;

use Closure;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Filament\Actions\UploadAttachmentAction;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Resources\AttachmentResource;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Set;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AttachmentInput extends Field
{
    protected string $view = 'filament-media-library::filament.attachment-input';

    protected bool|Closure $multiple = false;

    protected null|string|Closure $sortField = null;

    protected ?array $allowedFormats = null;


    protected function setUp(): void
    {
        parent::setUp();

        $this->registerActions([
            Action::make('remove-attachment')
                ->icon('heroicon-o-x-circle')
                ->iconButton()
                ->color('danger')
                ->size('sm')
                ->action(function (Set $set, array $arguments, $state) {
                    if ($this->isMultiple()) {
                        // TODO: delete $arguments['attachmentId'] from $state
                    } else {
                        $set($this->getStatePath(false), null);
                    }
                }),

            Action::make('format-attachment')
                ->icon('heroicon-o-scissors')
                ->iconButton()
                ->color('gray')
                ->size('sm'),

            Action::make('edit-attachment')
                ->icon('heroicon-s-pencil')
                ->iconButton()
                ->color('gray')
                ->size('sm')
                ->url(function (array $arguments): string {
                    return AttachmentResource::getUrl('edit', ['record' => $arguments['attachmentId']]);
                }, true),

            UploadAttachmentAction::make('attachment-upload')
                ->multiple(fn() => $this->isMultiple()),

            Action::make('attachment-picker')
                ->label(__('filament_media.select existing media'))
                ->color('gray'),
        ]);
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

    public function getAttachments(): Collection
    {
        $state = $this->getState();

        if ($this->isMultiple() && $state === null) {
            $relationship = $this->getRelationship();

            $state = $relationship->getResults()
                ->pluck($relationship->getRelatedKeyName())
                ->toArray();
        }

        if (blank($state) || empty($state)) {
            return collect();
        }

        $ids = collect($state)
            ->map(fn ($id) => "'{$id}'")
            ->join(',');

        return Attachment::whereIn('id', Arr::wrap($state))
            ->orderByRaw("FIELD(id,{$ids})")
            ->get();
    }

    public function sortField(string|Closure $sortField): static
    {
        $this->sortField = $sortField;

        return $this;
    }

    public function getSortField(): ?string
    {
        return $this->evaluate($this->sortField);
    }

    public function allowedFormats(?array $allowedFormats): static
    {
        $this->allowedFormats = $allowedFormats;

        return $this;
    }

    public function getAllowedFormats(): ?array
    {
        $formats = $this->evaluate($this->allowedFormats);

        // Get the model that we are editing/viewing
        if (is_null($formats)) {
            $model = $this->getModelInstance();

            if ($model) {
                $formats = $model->getFormats(collect())
                    ->filter(fn ($format) => $format->column() === $this->statePath);
            } else {
                $formats = Formats::all();
            }
        }

        return Collection::wrap($formats)
            ->map(fn ($format) => is_string($format) ? $format : get_class($format))
            ->toArray();
    }

    public function getRelationship(): BelongsToMany
    {
        return $this->getModelInstance()->{$this->getName()}();
    }
}
