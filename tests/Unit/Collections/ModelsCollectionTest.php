<?php

use Codedor\Attachments\Facades\Models;
use Codedor\Attachments\Tests\TestModels\TestModel;
use Codedor\Attachments\Tests\TestModels\TestModelNotRegisterable;
use Illuminate\Support\Collection;

it('returns registered models', function () {
    Models::add(TestModel::class);

    expect(Models::all())
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->first()->toBe(TestModel::class);
});

it('does not register model if not registerable', function () {
    expect(Models::add(TestModelNotRegisterable::class))
        ->toBeInstanceOf(\Codedor\Attachments\Collections\Models::class)
        ->all()
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});
