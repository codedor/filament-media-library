<?php

namespace Codedor\MediaLibrary\Filament;

use Codedor\MediaLibrary\Resources\AttachmentResource;
use Codedor\MediaLibrary\Resources\AttachmentTagResource;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Contracts\View\View;

class MediaLibraryPlugin implements Plugin
{
    protected bool $hasAttachmentResource = true;

    protected bool $hasAttachmentTagResource = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-media-library';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasAttachmentResource()) {
            $panel->resources([
                AttachmentResource::class,
            ]);
        }

        if ($this->hasAttachmentTagResource()) {
            $panel->resources([
                AttachmentTagResource::class,
            ]);
        }

        $panel->renderHook(
            'body.end',
            fn (): View => view($this->getId() . '::components.modals')
        );

        FilamentAsset::register([
//            Css::make('filament-media-library-stylesheet', __DIR__ . '/../../dist/css/laravel-media.css'),
//            Css::make('filament-media-library-cropper-stylesheet', __DIR__ . '/../../dist/css/cropper.min.css'),
//            Js::make('filament-media-library-script', __DIR__ . '/../../dist/js/cropper.min.js')
        ]);
    }

    public function boot(Panel $panel): void
    {
    }

    public function attachmentResource(bool $condition = true): static
    {
        $this->hasAttachmentResource = $condition;

        return $this;
    }

    public function hasAttachmentResource(): bool
    {
        return $this->hasAttachmentResource;
    }

    public function attachmentTagResource(bool $condition = true): static
    {
        $this->hasAttachmentTagResource = $condition;

        return $this;
    }

    public function hasAttachmentTagResource(): bool
    {
        return $this->hasAttachmentTagResource;
    }
}
