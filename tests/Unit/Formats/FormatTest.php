<?php

use Codedor\MediaLibrary\Conversions\LocalConversion;
use Codedor\MediaLibrary\Providers\MediaLibraryServiceProvider;
use Codedor\MediaLibrary\Tests\Fixtures\TestConversions\TestConversion;
use Codedor\MediaLibrary\Tests\Fixtures\TestFormats\TestHero;
use Illuminate\Support\Facades\Config;

it('returns default conversion', function () {
    $format = new TestHero('test');

    expect($format->conversion())
        ->toBeInstanceOf(LocalConversion::class);
});

it('returns defined conversion', function () {
    Config::set('filament-media-library.conversion', TestConversion::class);

    app(MediaLibraryServiceProvider::class, ['app' => app()])
        ->packageRegistered();

    $format = new TestHero('test');

    expect($format->conversion())
        ->toBeInstanceOf(TestConversion::class);
});
