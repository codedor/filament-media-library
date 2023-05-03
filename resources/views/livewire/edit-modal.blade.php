<x-filament::modal
    id="laravel-attachment::edit-attachment-modal"
    x-on:modal-closed="closeEditModal()"
    width="4xl"
>
    @if ($attachment)
        {{-- TODO: translated name? what does that mean? difference with alt name? --}}
        <x-filament::modal.heading>
            {{ __('laravel-attachment.edit modal heading :name', [
                'name' => $attachment->name,
            ]) }}
        </x-filament::modal.heading>

        <div class="py-8" wire:loading.remove>
            {{ $this->form }}
        </div>

        <div class="w-full justify-center py-8" wire:loading.flex>
            <x-filament-support::loading-indicator class="w-10 h-10" />
        </div>

        <x-filament::modal.actions>
            <x-filament::button color="secondary" x-on:click.prevent="close()">
                {{ __('laravel-attachment.cancel') }}
            </x-filament::button>

            <x-filament::button x-on:click.prevent="$wire.submit()">
                {{ __('laravel-attachment.confirm') }}
            </x-filament::button>
        </x-filament::modal.actions>
    @else
        <div class="w-full flex justify-center py-8">
            <x-filament-support::loading-indicator class="w-10 h-10" />
        </div>
    @endif
</x-filament::modal>
