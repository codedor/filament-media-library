<?php

use Codedor\Attachments\Pages\Library;
use Livewire\Livewire;

it('can render the library', function () {
    Livewire::test(Library::class)
        ->assertSee([
            'attachment.dashboard title',
            'attachment.open upload modal',
        ]);
});
