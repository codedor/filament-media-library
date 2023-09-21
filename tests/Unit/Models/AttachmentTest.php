<?php

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Tests\TestFormats\TestHero;
use Codedor\MediaLibrary\Tests\TestFormats\TestHeroWebp;
use Codedor\MediaLibrary\Tests\TestModels\TestModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('deletes root directory when attachment is removed from db', function () {
    Storage::fake('public');

    /** @var Attachment $attachment */
    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ]);

    $attachment->getStorage()->put(
        $attachment->file_path,
        File::get(__DIR__ . '/../../TestFiles/test.jpg')
    );

    $attachment->delete();

    Storage::assertMissing($attachment->directory);
});

it('returns the right url for a format', function () {
    Formats::register([TestHero::class, TestHeroWebp::class]);

    /** @var Attachment $attachment */
    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ]);

    expect($attachment->getFormat('test-hero'))
        ->toEndWith("test_hero__$attachment->filename");
});

it('returns null for a format that does not exist', function () {
    /** @var Attachment $attachment */
    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ]);

    expect($attachment->getFormat('format-does-not-exist'))
        ->toBeNull();
});

it('returns the original image when unknown format is requested', function () {
    /** @var Attachment $attachment */
    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ]);

    expect($attachment->getFormatOrOriginal('test-hero'))
        ->toEndWith("$attachment->filename");
});
