<?php

use Codedor\MediaLibrary\Conversions\LocalConversion;
use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Tests\TestFormats\TestHero;
use Codedor\MediaLibrary\Tests\TestFormats\TestHeroWebp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Spatie\Image\Image;

uses(RefreshDatabase::class);

it('skips generation if attachment is not an image', function () {
    Formats::register([TestHero::class, TestHeroWebp::class]);

    $attachment = createAttachment([
        'type' => 'not-an-image',
        'extension' => 'txt',
    ]);

    /** @var \Codedor\MediaLibrary\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, Formats::exists('test-hero')))
        ->toBeFalse();
});

it('skips generation if attachment is a gif', function () {
    Formats::register([TestHero::class, TestHeroWebp::class]);

    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'gif',
    ]);

    /** @var \Codedor\MediaLibrary\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, Formats::exists('test-hero')))
        ->toBeFalse();
});

it('skips generation if force is false and format image exists', function () {
    Storage::fake('public');
    Formats::register([TestHero::class, TestHeroWebp::class]);

    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ]);

    $attachment->getStorage()->put(
        "$attachment->directory/test_hero__$attachment->filename",
        File::get(__DIR__ . '/../../TestFiles/test.jpg')
    );

    /** @var \Codedor\MediaLibrary\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, Formats::exists('test-hero')))
        ->toBeFalse();
});

it('converts image', function () {
    Storage::fake('public');

    $this->mock(Image::class, function (MockInterface $mock) {
        $mock->shouldReceive('manipulate')
            ->andReturn('sdf');
    });

    Formats::register([TestHero::class, TestHeroWebp::class]);

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

    /** @var \Codedor\MediaLibrary\Formats\Format $format */
    $format = Formats::exists('test-hero');

    /** @var \Codedor\MediaLibrary\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, $format, true))
        ->toBeTrue();

    expect($attachment->getStorage()->path($attachment->directory . '/' . $format->filename($attachment)))
        ->toBeFile();
});

it('converts image to webp', function () {
    Storage::fake('public');

    $this->mock(Image::class, function (MockInterface $mock) {
        $mock->shouldReceive('manipulate')
            ->andReturn('sdf');
    });

    Formats::register([TestHero::class, TestHeroWebp::class]);

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

    /** @var \Codedor\MediaLibrary\Formats\Format $format */
    $format = Formats::exists('test-hero-webp');

    /** @var \Codedor\MediaLibrary\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, $format, true))
        ->toBeTrue();

    expect($attachment->getStorage()->path($attachment->directory . '/' . Str::replaceLast('jpg', 'webp', $format->filename($attachment))))
        ->toBeFile();
});
