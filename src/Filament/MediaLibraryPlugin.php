<?php

namespace Codedor\MediaLibrary\Filament;

use Codedor\MediaLibrary\Resources\AttachmentResource;
use Codedor\MediaLibrary\Resources\AttachmentTagResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

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
