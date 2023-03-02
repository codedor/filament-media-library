<?php

use Codedor\Attachments\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns the directory', function () {
    /** @var Attachment $attachment */
    $attachment = Attachment::factory([
        'id' => 1,
    ])->create();

    expect($attachment->directory)
        ->toBe('attachments/1');
});

it('returns the filename', function () {
    /** @var Attachment $attachment */
    $attachment = Attachment::factory([
        'name' => 'test-file',
    ])->create();

    expect($attachment->filename)
        ->toBe("test-file.$attachment->extension");
});
