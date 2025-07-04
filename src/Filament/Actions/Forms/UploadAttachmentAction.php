<?php

namespace Codedor\MediaLibrary\Filament\Actions\Forms;

use Codedor\MediaLibrary\Filament\Actions\Traits\CanUploadAttachment;
use Codedor\MediaLibrary\Models\Attachment;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Utilities\Set;
use Livewire\Component;

class UploadAttachmentAction extends \Filament\Actions\Action
{
    use CanUploadAttachment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (Component $livewire, Set $schemaSet, \Filament\Schemas\Components\Component $schemaComponent) {
            $this->saveAttachmentsAndSendNotification($livewire);

            // Set the state if this is a field
            $schemaSet(
                $schemaComponent->getStatePath(false),
                $this->isMultiple()
                    ? collect($schemaComponent->getState())->concat($attachmentIds)->toArray()
                    : $attachmentIds->first()
            );
        });
    }

    public function getModel(): ?string
    {
        return Attachment::class;
    }
}
