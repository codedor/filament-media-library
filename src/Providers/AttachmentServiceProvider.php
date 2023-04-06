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
use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Http\UploadedFile;
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
        'upload-modal' => Livewire\UploadModal::class,
        'edit-modal' => Livewire\EditModal::class,
        'picker' => Livewire\Picker::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::PACKAGE_NAME)
            ->setBasePath(__DIR__ . '/../')
            ->hasConfigFile('laravel-attachments')
            ->hasMigrations([
                '2022_08_03_120355_create_attachments_table',
                '2022_08_03_120356_create_attachment_tags_table',
                '2022_08_03_120357_create_attachment_attachment_tags_table',
            ])
            ->runsMigrations()
            ->hasViews('laravel-attachments');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        foreach ($this->livewireComponents as $key => $livewireComponent) {
            LivewireCore::component("{$this->packageName()}::$key", $livewireComponent);
        }

        UploadedFile::mixin(new UploadedFileMixin());
    }

    public function packageName(): string
    {
        return self::PACKAGE_NAME;
    }

    public function boot()
    {
        parent::boot();

        Filament::serving(function () {
            Filament::registerStyles([__DIR__ . '/../../dist/css/laravel-media.css']);
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
                'laravel-attachments.conversion',
                LocalConversion::class
            )
        );
    }
}
