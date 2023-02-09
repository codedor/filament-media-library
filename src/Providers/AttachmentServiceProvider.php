<?php

namespace Codedor\Attachments\Providers;

use Codedor\Attachments\Http\Livewire\UploadModal;
use Codedor\Attachments\Mixins\UploadedFileMixin;
use Codedor\Attachments\Pages\Library;
use Codedor\Attachments\Resources\AttachmentTagResource;
use Filament\PluginServiceProvider;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
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
        'upload-modal' => UploadModal::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name(self::PACKAGE_NAME)
            ->setBasePath(__DIR__ . '/../')
            ->hasConfigFile('laravel-attachments')
            ->hasMigrations([
                'create_attachments_table',
                'create_attachment_tags_table',
            ])
            ->runsMigrations()
            ->hasViews('laravel-attachments');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        foreach ($this->livewireComponents as $key => $livewireComponent) {
            Livewire::component("{$this->packageName()}::$key", $livewireComponent);
        }

        UploadedFile::mixin(new UploadedFileMixin());
    }

    public function packageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
