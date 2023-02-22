<?php

use Codedor\Attachments\Entities\Manipulations;
use Codedor\Attachments\Exceptions\ArgumentException;

beforeEach(function () {
    $this->manipulations = new Manipulations();
});

it('validates orientation', function () {
    $this->manipulations->orientation('not valid');
})->expectException(ArgumentException::class);

it('validates flip', function () {
    $this->manipulations->flip('not valid');
})->expectException(ArgumentException::class);

it('validates fit', function () {
    $this->manipulations->fit('not valid', 10, 10);
})->expectException(ArgumentException::class);

it('validates border type', function () {
    $this->manipulations->border(10, 'black', 'not valid');
})->expectException(ArgumentException::class);

it('validates format extenstion', function () {
    $this->manipulations->format('not valid');
})->expectException(ArgumentException::class);

it('validates filter type', function () {
    $this->manipulations->filter('not valid');
})->expectException(ArgumentException::class);

it('validates width', function () {
    $this->manipulations->width(-10);
})->expectException(ArgumentException::class);

it('validates height', function () {
    $this->manipulations->height(-10);
})->expectException(ArgumentException::class);

it('validates pos x percentage for focal crop', function () {
    $this->manipulations->cropWithFocal(100, 200, 110, 40, 2);
})->expectException(ArgumentException::class);

it('validates neg x percentage for focal crop', function () {
    $this->manipulations->cropWithFocal(100, 200, -20, 40, 2);
})->expectException(ArgumentException::class);

it('validates pos y percentage for focal crop', function () {
    $this->manipulations->cropWithFocal(100, 200, 40, 110, 2);
})->expectException(ArgumentException::class);

it('validates neg y percentage for focal crop', function () {
    $this->manipulations->cropWithFocal(100, 200, 40, -20, 2);
})->expectException(ArgumentException::class);

it('validates pos zoom for focal crop', function () {
    $this->manipulations->cropWithFocal(100, 200, 40, -100, 11);
})->expectException(ArgumentException::class);

it('validates neg zoom for focal crop', function () {
    $this->manipulations->cropWithFocal(100, 200, 40, -100, -1);
})->expectException(ArgumentException::class);
