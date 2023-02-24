<x-filament::modal id="laravel-attachment::delete-attachment-modal">
    <x-filament::modal.heading>
        {{ __('laravel-attachment.delete modal heading') }}
    </x-filament::modal.heading>

    <p class="py-1">
        {{ __('laravel-attachment.delete modal content') }}
    </p>

    <x-filament::modal.actions :full-width="true">
        <x-filament::button color="secondary" x-on:click.prevent="close()">
            {{ __('laravel-attachment.cancel') }}
        </x-filament::button>

        <x-filament::button color="danger" x-on:click.prevent="$wire.deleteAttachment() && close()">
            {{ __('laravel-attachment.confirm') }}
        </x-filament::button>
    </x-filament::modal.actions>
</x-filament::modal>
