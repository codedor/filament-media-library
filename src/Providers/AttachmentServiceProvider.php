<?php

namespace Codedor\Attachments\Providers;

use Codedor\Attachments\Collections\Formats;
use Codedor\Attachments\Conversions\Conversion;
use Codedor\Attachments\Conversions\LocalConversion;
use Codedor\Attachments\Facades\Models;
use Codedor\Attachments\Http\Livewire;
use Codedor\Attachments\Mixins\UploadedFileMixin;
use Codedor\Attachments\Pages\Library;
use Codedor\Attachments\Resources\AttachmentTagResource;
use Codedor\Attachments\Views\Picture;
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire as LivewireCore;
use Spatie\LaravelPackageTools\Package;

class AttachmentServiceProvider extends PluginServiceProvider
{
    protected const PACKAGE_NAME = 'laravel-attachments';

    protected array $pages = [
        Library::class,
    ];

    protected array $resources = [
        AttachmentTagResource::class,
    ];

    protected array $livewireComponents = [
        'formatter-modal' => Livewire\FormatterModal::class,
        'upload-modal' => Livewire\UploadModal::class,
        'edit-modal' => Livewire\EditModal::class,
        'picker' => Livewire\Picker::class,
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
        Blade::component(Picture::class, "{$this->packageName()}::picture");
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
    }
}
