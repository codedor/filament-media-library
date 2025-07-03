<?php

use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Resources\AttachmentResource\Pages\ListAttachments;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Livewire;

it('can render the upload attachment action', function () {
    Livewire::test(ListAttachments::class)
        ->assertActionExists('uploadAttachment');
});

it('can mount the upload attachment action and fill the form', function () {
    $file = TemporaryUploadedFile::fake()->image('avatar.png');

    Livewire::test(ListAttachments::class)
        ->mountAction('uploadAttachment')
        ->goToNextWizardStep()
        ->assertHasFormErrors(['attachments'])
        ->fillForm([
            'attachments' => [$file],
        ])
        ->goToNextWizardStep()
        ->assertHasNoFormErrors(['attachments'])
        ->assertHasNoFormErrors()
        ->callMountedAction();

    expect(Attachment::first())
        ->name->toBe('avatar')
        ->extension->toBe('png')
        ->type->toBe('image');
});
