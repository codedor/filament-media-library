<?php

namespace Codedor\MediaLibrary\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Codedor\MediaLibrary\Providers\MediaLibraryServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Spatie\Image\Manipulations;
use Spatie\Translatable\TranslatableServiceProvider;

class TestCase extends Orchestra
{
    public Manipulations $manipulations;

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('filament-media-library.enable-webp-generation', false);
    }

    protected function getPackageProviders($app)
    {
        return [
            MediaLibraryServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            TranslatableServiceProvider::class,
        ];
    }
}
