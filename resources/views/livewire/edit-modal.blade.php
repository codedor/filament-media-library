<div>
    @if ($attachment)
        <x-filament::modal.heading>
            {{ __('laravel-attachment.edit modal heading :name', [
                'name' => $attachment->name,
            ]) }}
        </x-filament::modal.heading>

        <div class="py-8">
            {{ $this->form }}
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
</div>
