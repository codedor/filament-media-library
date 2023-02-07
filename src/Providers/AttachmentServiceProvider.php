<?php

namespace Codedor\Attachments\Providers;

use Codedor\Attachments\Http\Livewire\UploadModal;
use Codedor\Attachments\Pages\Library;
use Filament\PluginServiceProvider;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;

class AttachmentServiceProvider extends PluginServiceProvider
{
    protected const PACKAGE_NAME = 'laravel-attachments';

    protected array $pages = [
        Library::class,
    ];

    protected array $livewireComponents = [
        'upload-modal' => UploadModal::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::PACKAGE_NAME)
            ->setBasePath(__DIR__ . '/../')
            ->hasMigrations([
                'create_attachments_table',
            ])
            ->hasViews('laravel-attachments');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        foreach ($this->livewireComponents as $key => $livewireComponent) {
            Livewire::component("{$this->packageName()}::$key", $livewireComponent);
        }
    }

    public function packageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
