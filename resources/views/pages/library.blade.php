<x-filament::page>
    <div
        x-data="{
            search: @entangle('search'),
            resetFilters () {
                this.search = ''
            },
            openDeleteModal (id) {
                $wire.set('attachmentToDelete', id)
                $dispatch('open-modal', { id: 'laravel-attachment::delete-attachment-modal' })
            },
            openFormatterModal (id) {
                $dispatch('open-modal', { id: 'laravel-attachment::formatter-attachment-modal' })
                $wire.emit('laravel-attachment::open-formatter-attachment-modal', id)
            },
            openEditModal (id) {
                $dispatch('open-modal', { id: 'laravel-attachment::edit-attachment-modal' })
                $wire.emit('laravel-attachment::open-edit-attachment-modal', id)
            },
            closeEditModal () {
                $wire.emit('laravel-attachment::close-edit-attachment-modal')
            },
        }"
    >
        <div class="gallery-container flex flex-col gap-8">
            <div class="w-full flex justify-between">
                <div class="flex flex-col gap-1 w-1/3">
                    <label for="search">{{ __('laravel-attachment.search filter label') }}</label>
                    <input
                        id="search"
                        wire:model.debounce.500ms="search"
                        placeholder="{{ __('laravel-attachment.search') }}"
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
                    {{ __('laravel-attachment.clear filter') }}
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
                        <x-laravel-attachments::attachment
                            :$attachment
                            delete-action="openDeleteModal('{{ $attachment->id }}')"
                            edit-action="openEditModal('{{ $attachment->id }}')"
                            formatter-action="openFormatterModal('{{ $attachment->id }}')"
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

        @include('laravel-attachments::livewire.delete-modal')

        @livewire('laravel-attachments::upload-modal')
        @livewire('laravel-attachments::edit-modal')
        @livewire('laravel-attachments::formatter-modal')
    </div>
</x-filament::page>
