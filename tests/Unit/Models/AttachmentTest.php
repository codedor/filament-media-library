<?php

use Codedor\Attachments\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('deletes root directory when attachment is removed from db', function () {
    Storage::fake('public');

    /** @var Attachment $attachment */
    $attachment = Attachment::factory([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ])->create();

    $attachment->getStorage()->put(
        $attachment->file_path,
        File::get(__DIR__ . '/../../TestFiles/test.jpg')
    );

    $attachment->delete();

    Storage::assertMissing($attachment->directory);
});
