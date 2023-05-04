<x-filament::modal id="laravel-attachment::delete-attachment-modal" width="lg">
    <x-slot name="header">
        <x-filament::modal.heading>
            {{ __('laravel-attachment.delete modal heading') }}
        </x-filament::modal.heading>
    </x-slot>

    <x-slot name="heading">
        <p class="py-1">
            {{ __('laravel-attachment.delete modal content') }}
        </p>
    </x-slot>

    <x-filament::modal.actions :full-width="true">
        <x-filament::button x-on:click.prevent="close()">
            {{ __('laravel-attachment.cancel') }}
        </x-filament::button>

        <x-filament::button color="danger" outlined x-on:click.prevent="$wire.deleteAttachment() && close()">
            {{ __('laravel-attachment.confirm') }}
        </x-filament::button>
    </x-filament::modal.actions>
</x-filament::modal>
