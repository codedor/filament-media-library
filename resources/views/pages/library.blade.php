<x-filament::page>
    <div
        x-data="{
            search: @entangle('search'),
            resetFilters () {
                this.search = ''
            },
            openDeleteModal (id) {
                $wire.set('attachmentToDelete', id)
                $dispatch('open-modal', { id: 'filament-media-library::delete-attachment-modal' })
            }
        }"
    >
        <div class="gallery-container flex flex-col gap-8">
            <div class="w-full flex justify-between">
                <div class="flex flex-col gap-1 w-1/3">
                    <label for="search">{{ __('filament_media.search filter label') }}</label>
                    <input
                        id="search"
                        wire:model.debounce.500ms="search"
                        placeholder="{{ __('filament_media.search') }}"
                        type="text"
                        class="
                            block w-full transition duration-75 rounded-lg shadow-sm outline-none
                            focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500
                            disabled:opacity-70 border-gray-300
                        "
                    >
                </div>

                <div class="w-1/3"></div>

                <button class="flex gap-2 p-3 items-center" x-on:click="resetFilters()">
                    <x-heroicon-o-x class="w-5 h-5" />
                    {{ __('filament_media.clear filter') }}
                </button>
            </div>

            <div
                wire:loading.flex
                wire:target="search"
                class="w-full h-128 justify-center items-center"
            >
                <x-filament-support::loading-indicator
                    class="w-10 h-10"
                />
            </div>

            <div
                wire:loading.remove
                wire:target="search"
                class="gallery"
            >
                @foreach($attachments as $attachment)
                    <div class="p-2 rounded-lg overflow-hidden shadow-lg bg-white flex flex-col gap-2">
                        <x-filament-media-library::attachment
                            :$attachment
                            delete-action="openDeleteModal('{{ $attachment->id }}')"
                            edit-action="openEditModal('{{ $attachment->id }}')"
                            formatter-action="openFormatterModal('{{ $attachment->id }}')"
                            :extendedTooltip="true"
                        />
                    </div>
                @endforeach
            </div>

            <div
                wire:loading.remove
                wire:target="search"
            >
                {{ $attachments->links() }}
            </div>
        </div>
    </div>
</x-filament::page>
