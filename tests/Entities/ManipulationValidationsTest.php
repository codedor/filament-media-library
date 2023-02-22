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
