<?php

use Codedor\Attachments\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

it('can save an image on default public disk', function () {
    Storage::fake('public');

    assertDatabaseCount(Attachment::class, 0);

    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    $file->save();

    assertDatabaseCount(Attachment::class, 1);

    assertDatabaseHas(Attachment::class, [
        'name' => 'test',
        'extension' => 'jpg',
        'mime_type' => 'image/jpeg',
        'md5' => md5_file($file->path()),
        'type' => 'image',
        'size' => $file->getSize(),
        'width' => 100,
        'height' => 100,
        'disk' => 'public',
    ]);

    /** @var Attachment $attachment */
    $attachment = Attachment::first();

    Storage::disk('public')->assertExists($attachment->directory());
});

it('can save an image on default other disk', function () {
    $disk = 'local';

    Storage::fake($disk);

    assertDatabaseCount(Attachment::class, 0);

    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    $file->save($disk);

    assertDatabaseCount(Attachment::class, 1);

    assertDatabaseHas(Attachment::class, [
        'name' => 'test',
        'extension' => 'jpg',
        'mime_type' => 'image/jpeg',
        'md5' => md5_file($file->path()),
        'type' => 'image',
        'size' => $file->getSize(),
        'width' => 100,
        'height' => 100,
        'disk' => $disk,
    ]);

    /** @var Attachment $attachment */
    $attachment = Attachment::first();

    Storage::disk($disk)->assertExists($attachment->directory());
});
