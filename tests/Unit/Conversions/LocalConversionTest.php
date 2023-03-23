<?php

use Codedor\Attachments\Conversions\LocalConversion;
use Codedor\Attachments\Facades\Formats;
use Codedor\Attachments\Facades\Models;
use Codedor\Attachments\Models\Attachment;
use Codedor\Attachments\Tests\TestModels\TestModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Spatie\Image\Image;

uses(RefreshDatabase::class);

it('skips generation if attachment is not an image', function () {
    Models::add(TestModel::class);

    $attachment = Attachment::factory([
        'type' => 'not-an-image',
    ])->create();

    /** @var \Codedor\Attachments\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, Formats::exists('test-hero')))
        ->toBeFalse();
});

it('skips generation if attachment is a gif', function () {
    Models::add(TestModel::class);

    $attachment = Attachment::factory([
        'type' => 'image',
        'extension' => 'gif',
    ])->create();

    /** @var \Codedor\Attachments\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, Formats::exists('test-hero')))
        ->toBeFalse();
});

it('skips generation if force is false and format image exists', function () {
    Storage::fake('public');
    Models::add(TestModel::class);

    $attachment = Attachment::factory([
        'type' => 'image',
        'disk' => 'public',
    ])->create();

    /** @var \Codedor\Attachments\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, Formats::exists('test-hero')))
        ->toBeFalse();
});

it('skips generation if force is true and format image exists', function () {
    Storage::fake('public');

    $this->mock(Image::class, function (MockInterface $mock) {
        $mock->shouldReceive('manipulate')
            ->andReturn('sdf');
    });

    Models::add(TestModel::class);

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

    /** @var \Codedor\Attachments\Formats\Format $format */
    $format = Formats::exists('test-hero');

    /** @var \Codedor\Attachments\Conversions\Conversion $conversion */
    $conversion = app(LocalConversion::class);

    expect($conversion->convert($attachment, $format, true))
        ->toBeTrue();

    expect($attachment->getStorage()->path($attachment->directory . '/' . $format->filename($attachment)))
        ->toBeFile();
});
