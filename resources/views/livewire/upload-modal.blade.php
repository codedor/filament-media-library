<x-filament::modal
    id="filament-media-library::upload-attachment-modal"
    width="4xl"
>
    <x-slot name="header">
        <x-filament::modal.heading>
            {{ __('filament_media.upload modal heading') }}
        </x-filament::modal.heading>
    </x-slot>

    {{ $this->form }}

    <x-slot name="footer">
{{--        <x-filament::modal.actions>--}}
            <x-filament::button wire:click.prevent="submit" type="submit">
                {{ __('filament_media.submit') }}
            </x-filament::button>
{{--        </x-filament::modal.actions>--}}
    </x-slot>
</x-filament::modal>
