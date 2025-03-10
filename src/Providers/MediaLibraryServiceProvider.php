<?php

namespace Codedor\MediaLibrary\Providers;

use Codedor\MediaLibrary\Collections\Formats;
use Codedor\MediaLibrary\Commands\GenerateFormats;
use Codedor\MediaLibrary\Conversions\Conversion;
use Codedor\MediaLibrary\Conversions\LocalConversion;
use Codedor\MediaLibrary\Facades\Formats as FacadesFormats;
use Codedor\MediaLibrary\Formats\Lazyload;
use Codedor\MediaLibrary\Formats\Thumbnail;
use Codedor\MediaLibrary\Livewire;
use Codedor\MediaLibrary\Mixins\UploadedFileMixin;
use Codedor\MediaLibrary\Views\Picture;
use Codedor\MediaLibrary\Views\Placeholder;
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
                '2025_01_30_130345_add_is_hidden_to_attachment_tags',
            ])
            ->runsMigrations()
            ->hasViews($this->packageName())
            ->hasCommands([
                GenerateFormats::class,
            ])
            ->hasTranslations();
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

        UploadedFile::mixin(new UploadedFileMixin);
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

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton(Formats::class, fn () => new Formats);

        $this->app->bind(Conversion::class, config(
            "{$this->packageName()}.conversion",
            LocalConversion::class
        ));

        FacadesFormats::register([
            Lazyload::class,
            Thumbnail::class,
        ]);
    }
}
