<x-filament::modal
    id="laravel-attachment::upload-attachment-modal{{ $statePath }}"
    width="4xl"
>
    <x-slot name="header">
        <x-filament::modal.heading>
            {{ __('laravel-attachment.upload modal heading') }}
        </x-filament::modal.heading>
    </x-slot>

    {{ $this->form }}

    <x-slot name="footer">
        <x-filament::modal.actions>
            <x-filament::button wire:click.prevent="submit" type="submit">
                {{ __('laravel-attachment.submit') }}
            </x-filament::button>
        </x-filament::modal.actions>
    </x-slot>
</x-filament::modal>
