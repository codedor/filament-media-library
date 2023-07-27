<?php

namespace Codedor\MediaLibrary\Components\Fields;

use Closure;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AttachmentInput extends Field implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament-media-library::components.fields.attachment-input';

    public array|Attachment $pickedAttachments = [];

    public bool|Closure $multiple = false;

    public null|string|Closure $sortField = null;

    public Closure $attachmentsListQuery;

    public ?array $allowedFormats = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->attachmentsListQuery(function () {
            return Attachment::latest()->get();
        });

        $this->saveRelationshipsUsing(static function (self $component, $state) {
            if (! $component->isMultiple()) {
                return;
            }

            // If the state is null, it means that the field has not been touched (and the state was never set)
            // So we don't want to sync the relationship, because it would remove all the existing attachments
            if ($state === null) {
                return;
            }

            $state = Collection::wrap($state ?? []);
            $sortField = $component->getSortField();

            if (is_string($sortField)) {
                $state = $state->mapWithKeys(function ($item, $index) use ($sortField) {
                    return [$item => [$sortField => $index + 10000]];
                });
            }

            $component->getRelationship()->detach();
            $component->getRelationship()->sync($state->toArray());
        });

        $this->dehydrateStateUsing(function (self $component, $state) {
            if ($component->isMultiple()) {
                return json_encode($state);
            }

            return $state[0] ?? null;
        });
    }

    public function setPickedAttachments(array|Collection $attachments): void
    {
        $this->pickedAttachments = Collection::wrap($attachments)->unique();
    }

    public function getPickedAttachments(): Collection
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

        $ids = collect($state)->map(fn ($id) => "'{$id}'")->join(',');

        return Attachment::whereIn('id', Arr::wrap($state))
            ->orderByRaw("FIELD(id,{$ids})")
            ->get();
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

    public function getRelationship(): BelongsToMany
    {
        return $this->getModelInstance()->{$this->getName()}();
    }

    public function attachmentsListQuery(Closure $query): static
    {
        $this->attachmentsListQuery = $query;

        return $this;
    }

    public function getAttachmentsList()
    {
        return $this->evaluate($this->attachmentsListQuery);
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
}
