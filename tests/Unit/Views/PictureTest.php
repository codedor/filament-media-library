<?php

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Tests\TestFormats\TestHero;
use Codedor\MediaLibrary\Tests\TestFormats\TestNoHeight;
use Codedor\MediaLibrary\Views\Picture;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the picture component', function () {
    $this->component(Picture::class)
        ->assertSee('');
});

it('can render the picture component with placeholder', function () {
    Formats::register([TestHero::class]);

    $this->component(Picture::class, [
        'placeholder' => true,
        'format' => 'test-hero',
        'pictureClass' => 'test',
        'image' => createAttachment(),
    ])
        ->assertSee("https://via.placeholder.com/100x100/21348c/ffffff.webp?text=Test Hero 100 x 100");
});

it('can render the picture component with an image and format', function () {
    Formats::register([TestHero::class]);

    $this->component(Picture::class, [
        'format' => 'test-hero',
        'pictureClass' => 'test',
        'image' => createAttachment(),
    ])
        ->assertSee('width="100"', false)
        ->assertSee('height="100"', false);
});

it('can render the picture component with an image and format but will not add height or width if one of both is missing on the format', function () {
    Formats::register([TestNoHeight::class]);

    $this->component(Picture::class, [
        'format' => 'test-no-height',
        'pictureClass' => 'test',
        'image' => createAttachment(),
    ])
        ->assertDontSee('width=', false)
        ->assertDontSee('height=', false);
});
