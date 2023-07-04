<?php

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Tests\TestFormats\TestHero;
use Codedor\MediaLibrary\Tests\TestModels\TestModel;

it('registers model', function () {
    expect(Formats::registerForModel(TestModel::class))
        ->toBeInstanceOf(\Codedor\MediaLibrary\Collections\Formats::class)
        ->toHaveKey(TestModel::class)
        ->first()
        ->toHaveCount(2)
        ->first()
        ->first()
        ->toBeInstanceOf(TestHero::class);
});

it('returns format if format is registered', function () {
    Formats::registerForModel(TestModel::class);

    expect(Formats::exists('test-hero'))
        ->toBeInstanceOf(TestHero::class);

    expect(Formats::exists('format-does-not-exist'))
        ->toBeNull();
});

it('returns null if format is not registered', function () {
    Formats::registerForModel(TestModel::class);

    expect(Formats::exists('format-does-not-exist'))
        ->toBeNull();
});

it('returns collection with kebab keys', function () {
    Formats::registerForModel(TestModel::class);

    expect(Formats::mapToKebab())
        ->toHaveKey('test-hero')
        ->toHaveKey('test-hero-webp')
        ->toHaveCount(2);
});
