<?php

use Codedor\MediaLibrary\Facades\Formats;
use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Tests\TestFormats\TestHero;
use Codedor\MediaLibrary\Tests\TestFormats\TestHeroWebp;
use Codedor\MediaLibrary\Tests\TestModels\TestModel;

it('registers model', function () {
    expect(Formats::register([TestHero::class]))
        ->toBeInstanceOf(\Codedor\MediaLibrary\Collections\Formats::class)
        ->toHaveKey(TestModel::class)
        ->first()
        ->first()
        ->toBeInstanceOf(Format::class);
});

it('returns format if format is registered', function () {
    Formats::register([TestHero::class, TestHeroWebp::class]);

    expect(Formats::exists('test-hero'))
        ->toBeInstanceOf(TestHero::class);

    expect(Formats::exists('format-does-not-exist'))
        ->toBeNull();
});

it('returns null if format is not registered', function () {
    Formats::register([TestHero::class, TestHeroWebp::class]);

    expect(Formats::exists('format-does-not-exist'))
        ->toBeNull();
});

it('returns collection with kebab keys', function () {
    Formats::register([TestHero::class, TestHeroWebp::class]);

    expect(Formats::mapToKebab())
        ->toHaveKey('test-hero')
        ->toHaveKey('test-hero-webp');
});
