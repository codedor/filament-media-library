<?php

use Codedor\Attachments\Entities\Manipulations;

beforeEach(function () {
    $this->manipulations = new Manipulations();
});

it('adds orientation manipulation', function ($value) {
    $this->manipulations->orientation($value);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe(['orientation' => $value]);
})->with([
    'auto',
    '0',
    '90',
    '180',
    '270',
]);

it('adds flip manipulation', function ($value) {
    $this->manipulations->flip($value);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe(['flip' => $value]);
})->with([
    'h',
    'v',
    'both',
]);

it('adds fit manipulation', function ($location) {
    $this->manipulations->fit($location, 200, 10);

    expect($this->manipulations->all())
        ->toHaveCount(3)
        ->toBe([
            'width' => 200,
            'height' => 10,
            'fit' => $location,
        ]);
})->with([
    'crop-top-left',
    'crop-top',
    'crop-top-right',
    'crop-left',
    'crop-center',
    'crop-right',
    'crop-bottom-left',
    'crop-bottom',
    'crop-bottom-right',
]);

it('adds crop with focal manipulation', function () {
    $this->manipulations->cropWithFocal(100, 200, 10, 40, 2);

    expect($this->manipulations->all())
        ->toHaveCount(3)
        ->toBe([
            'width' => 100,
            'height' => 200,
            'fit' => 'crop-10-40-2',
        ]);
});

it('adds crop manipulation', function () {
    $this->manipulations->crop(100, 200, 10, 20);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'crop' => '100,200,10,20',
        ]);
});

it('adds width manipulation', function () {
    $this->manipulations->width(100);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'width' => 100,
        ]);
});

it('adds height manipulation', function () {
    $this->manipulations->height(100);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'height' => 100,
        ]);
});

it('adds brightness manipulation', function () {
    $this->manipulations->brightness(55);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'brightness' => 55,
        ]);
});

it('adds gamma manipulation', function () {
    $this->manipulations->gamma(55);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'gamma' => 55.0,
        ]);
});

it('adds contrast manipulation', function () {
    $this->manipulations->contrast(55);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'contrast' => 55,
        ]);
});

it('adds blur manipulation', function () {
    $this->manipulations->blur(55);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'blur' => 55,
        ]);
});

it('adds pixelate manipulation', function () {
    $this->manipulations->pixelate(55);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'pixelate' => 55,
        ]);
});

it('adds sepia manipulation', function () {
    $this->manipulations->sepia();

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'filter' => 'sepia',
        ]);
});

it('adds greyscale manipulation', function () {
    $this->manipulations->greyscale();

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'filter' => 'greyscale',
        ]);
});

it('adds filter manipulation', function ($filter) {
    $this->manipulations->filter($filter);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'filter' => $filter,
        ]);
})->with([
    'greyscale',
    'sepia',
]);

it('adds format manipulation', function ($format) {
    $this->manipulations->format($format);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'format' => $format,
        ]);
})->with([
    'jpg',
    'pjpg',
    'png',
    'gif',
    'webp',
    'avif',
    'tiff',
]);

it('adds border manipulation', function ($type) {
    $this->manipulations->border(10, 'black', $type);

    expect($this->manipulations->all())
        ->toHaveCount(1)
        ->toBe([
            'border' => "10,black,$type",
        ]);
})->with([
    'overlay',
    'shrink',
    'expand',
]);
