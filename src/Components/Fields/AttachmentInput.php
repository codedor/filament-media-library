<?php

namespace Codedor\Attachments\Components\Fields;

use Closure;
use Codedor\Attachments\Models\Attachment;
use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AttachmentInput extends Field implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'laravel-attachments::components.fields.attachment-input';

    public array|Attachment $pickedAttachments = [];

    public bool|Closure $multiple = false;

    public Closure $attachmentsListQuery;

    public function setUp(): void
    {
        parent::setUp();

        $this->attachmentsListQuery(function () {
            return Attachment::orderBy('id', 'desc')->get();
        });

        // Don't save the relationship if the field is not multiple
        $this->dehydrated($this->isMultiple());

        $this->saveRelationshipsUsing(static function (self $component, $state) {
            if (! $component->isMultiple()) {
                return;
            }

            // If the state is null, it means that the field has not been touched (and the state was never set)
            // So we don't want to sync the relationship, because it would remove all the existing attachments
            if ($state === null) {
                return;
            }

            $component->getRelationship()->sync($state);
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

        return Attachment::whereIn('id', Arr::wrap($state))
            ->orderBy('id', 'desc') // TODO: global scope?
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
}
