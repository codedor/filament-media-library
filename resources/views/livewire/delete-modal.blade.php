<x-filament::modal id="laravel-attachment::delete-attachment-modal" width="lg">
    <x-slot name="header">
        <x-filament::modal.heading>
            {{ __('filament_media.delete modal heading') }}
        </x-filament::modal.heading>
    </x-slot>

    <x-slot name="heading">
        <p class="py-1">
            {{ __('filament_media.delete modal content') }}
        </p>
    </x-slot>

    <x-filament::modal.actions :full-width="true">
        <x-filament::button color="secondary" x-on:click.prevent="close()">
            {{ __('filament_media.cancel') }}
        </x-filament::button>

        <x-filament::button color="danger" outlined x-on:click.prevent="$wire.deleteAttachment() && close()">
            {{ __('filament_media.confirm') }}
        </x-filament::button>
    </x-filament::modal.actions>
</x-filament::modal>
