<?php

use Codedor\MediaLibrary\Facades\Models;
use Codedor\MediaLibrary\Tests\TestModels\TestModel;
use Codedor\MediaLibrary\Tests\TestModels\TestModelNotRegisterable;
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
        ->toBeInstanceOf(\Codedor\MediaLibrary\Collections\Models::class)
        ->all()
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});
