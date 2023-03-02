<?php

use Codedor\Attachments\Pages\Library;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('can render the library', function () {
    Livewire::test(Library::class)
        ->assertOk();
});
