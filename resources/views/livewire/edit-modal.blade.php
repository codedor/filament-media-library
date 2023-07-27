<x-filament::modal
    id="filament-media-library::edit-attachment-modal"
    x-on:modal-closed="$wire.dispatch('filament-media-library::close-edit-attachment-modal')"
    width="4xl"
>
    @if ($attachment)
        {{-- TODO: translated name? what does that mean? difference with alt name? --}}
        <x-slot name="header">
            <x-filament::modal.heading>
                {{ __('filament_media.edit modal heading :name', [
                    'name' => $attachment->name,
                ]) }}
            </x-filament::modal.heading>
        </x-slot>

        <div class="py-8" wire:loading.remove>
            {{ $this->form }}
        </div>

        <div class="w-full justify-center py-8" wire:loading.flex>
            <x-filament::loading-indicator class="w-10 h-10" />
        </div>

        <x-slot name="footer">
{{--            <x-filament::modal.actions>--}}
                <x-filament::button color="secondary" x-on:click.prevent="close()">
                    {{ __('filament_media.cancel') }}
                </x-filament::button>

                <x-filament::button x-on:click.prevent="$wire.submit()">
                    {{ __('filament_media.confirm') }}
                </x-filament::button>
{{--            </x-filament::modal.actions>--}}
        </x-slot>
    @else
        <div class="w-full flex justify-center py-8">
            <x-filament::loading-indicator class="w-10 h-10" />
        </div>
    @endif
</x-filament::modal>
