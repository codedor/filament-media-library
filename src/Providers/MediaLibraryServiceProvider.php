<?php

namespace Codedor\MediaLibrary\Providers;

use BladeUI\Icons\Factory;
use Codedor\MediaLibrary\Collections\Formats;
use Codedor\MediaLibrary\Conversions\Conversion;
use Codedor\MediaLibrary\Conversions\LocalConversion;
use Codedor\MediaLibrary\Facades\Models;
use Codedor\MediaLibrary\Http\Livewire;
use Codedor\MediaLibrary\Mixins\UploadedFileMixin;
use Codedor\MediaLibrary\Views\Picture;
use Codedor\MediaLibrary\Views\Placeholder;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire as LivewireCore;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MediaLibraryServiceProvider extends PackageServiceProvider
{
    protected const PACKAGE_NAME = 'filament-media-library';

    protected array $livewireComponents = [
        'formatter-modal' => Livewire\FormatterModal::class,
        'upload-modal' => Livewire\UploadModal::class,
        'edit-modal' => Livewire\EditModal::class,
        'picker' => Livewire\Picker::class,
    ];

    protected array $bladeComponents = [
        'picture' => Picture::class,
        'placeholder' => Placeholder::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name($this->packageName())
            ->setBasePath(__DIR__ . '/../')
            ->hasConfigFile($this->packageName())
            ->hasMigrations([
                '2022_08_03_120355_create_attachments_table',
                '2022_08_03_120356_create_attachment_tags_table',
                '2022_08_03_120357_create_attachment_attachment_tags_table',
                '2023_04_27_120359_create_attachment_formats',
            ])
            ->runsMigrations()
            ->hasViews($this->packageName());
    }

    public function packageName(): string
    {
        return self::PACKAGE_NAME;
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->registerLivewireComponents();
        $this->registerBladeComponents();

        UploadedFile::mixin(new UploadedFileMixin());
    }

    protected function registerLivewireComponents()
    {
        foreach ($this->livewireComponents as $key => $livewireComponent) {
            LivewireCore::component("{$this->packageName()}::$key", $livewireComponent);
        }
    }

    protected function registerBladeComponents()
    {
        foreach ($this->bladeComponents as $view => $class) {
            Blade::component($class, "{$this->packageName()}::$view");
        }
    }

    public function boot()
    {
        parent::boot();

        Filament::serving(function () {
            Filament::registerStyles([
                __DIR__ . '/../../dist/css/laravel-media.css',
                __DIR__ . '/../../dist/css/cropper.min.css',
            ]);

            Filament::registerScripts([
                __DIR__ . '/../../dist/js/cropper.min.js',
            ]);

            Filament::registerRenderHook(
                'body.end',
                fn (): View => view(self::PACKAGE_NAME . '::components.modals')
            );
        });
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton(Formats::class, function () {
            return new Formats();
        });

        $this->app->singleton(Models::class, function () {
            return new Models();
        });

        $this->app->bind(
            Conversion::class,
            config(
                "{$this->packageName()}.conversion",
                LocalConversion::class
            )
        );

        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('attachments', [
                'path' => __DIR__ . '/../../resources/svg',
                'prefix' => 'attachments',
            ]);
        });
    }
}
