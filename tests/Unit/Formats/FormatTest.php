<?php

use Codedor\Attachments\Conversions\LocalConversion;
use Codedor\Attachments\Providers\AttachmentServiceProvider;
use Codedor\Attachments\Tests\TestConversions\TestConversion;
use Codedor\Attachments\Tests\TestFormats\TestHero;
use Illuminate\Support\Facades\Config;

it('returns default conversion', function () {
    $format = new TestHero('test');

    expect($format->conversion())
        ->toBeInstanceOf(LocalConversion::class);
});

it('returns defined conversion', function () {
    Config::set('laravel-attachments.conversion', TestConversion::class);

    app(AttachmentServiceProvider::class, ['app' => app()])
        ->packageRegistered();

    $format = new TestHero('test');

    expect($format->conversion())
        ->toBeInstanceOf(TestConversion::class);
});
