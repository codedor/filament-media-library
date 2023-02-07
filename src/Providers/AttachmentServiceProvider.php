<?php

namespace Codedor\Attachments\Providers;

use Codedor\Attachments\Pages\Library;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class AttachmentServiceProvider extends PluginServiceProvider
{
    protected array $pages = [
        Library::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-attachments')
            ->setBasePath(__DIR__ . '/../')
            ->hasMigrations([
                'create_attachments_table',
            ])
            ->hasViews('laravel-attachments');
    }
}
