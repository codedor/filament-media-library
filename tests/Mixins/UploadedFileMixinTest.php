<?php

use Codedor\Attachments\Entities\Dimension;
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

it('does a check if file is an image', function () {
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    expect($file->isImage())->toBeTrue();

    $file = UploadedFile::fake()->create(
        'file.pdf',
        100,
        'application/pdf'
    );
    expect($file->isImage())->toBeFalse();
});

it('returns the dimensions for an image', function () {
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);

    expect($file->dimensions())
        ->toBeInstanceOf(Dimension::class)
        ->height->toBe(100)
        ->width->toBe(100);
});

it('returns no dimensions if mimetype is image/svg+xml', function () {
    $file = UploadedFile::fake()->create('test.svg', 10, 'image/svg+xml');

    expect($file->dimensions())
        ->toBeNull();
});

it('returns no dimensions if mimetype is image/svg', function () {
    $file = UploadedFile::fake()->create('test.svg', 10, 'image/svg');

    expect($file->dimensions())
        ->toBeNull();
});

it('returns right file type', function ($type, $extension) {
    $file = UploadedFile::fake()->create("test.$extension");

    expect($file->fileType())
        ->toBe($type);
})->with('filetypes');
