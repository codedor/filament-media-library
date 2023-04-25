<x-filament::page>
    <div
        class="flex flex-col gap-8"
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
            wire:target="search, nextPage, gotoPage, previousPage"
            class="w-full h-128 justify-center items-center"
        >
            <x-filament-support::loading-indicator
                class="w-10 h-10"
            />
        </div>

        <div
            wire:loading.remove
            wire:target="search, nextPage, gotoPage, previousPage"
            class="grid grid-cols-6 gap-4"
        >
            @foreach($attachments as $attachment)
                <div class="p-2 rounded-lg overflow-hidden shadow-lg bg-white flex flex-col gap-2">
                    <p class="font-bold text-center w-full text-sm">
                        {{ Str::limit($attachment->translated_name, 15) }}
                    </p>

                    @if($attachment->type === 'image')
                        <x-laravel-attachments::attachment :$attachment />
                    @else
                        <div class="w-full aspect-square flex items-center justify-center bg-gray-100 border rounded-lg">
                            @if($attachment->type === 'document')
                                <x-heroicon-o-document-text class="w-16 h-16 opacity-50" />
                            @elseif($attachment->type === 'video')
                                <x-heroicon-o-video-camera class="w-16 h-16 opacity-50" />
                            @else
                                <x-heroicon-o-question-mark-circle class="w-16 h-16 opacity-50" />
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-end gap-2">
                        <div
                            class="p-1 bg-gray-100 border rounded-lg cursor-pointer hover:bg-gray-200"
                            x-on:click="openFormatterModal('{{ $attachment->id }}')"
                        >
                            <x-heroicon-s-scissors class="w-4 h-4" />
                        </div>

                        <div
                            class="p-1 bg-gray-100 border rounded-lg cursor-pointer hover:bg-gray-200"
                            x-on:click="openEditModal('{{ $attachment->id }}')"
                        >
                            <x-heroicon-s-pencil class="w-4 h-4" />
                        </div>

                        <div
                            class="p-1 bg-gray-100 border rounded-lg cursor-pointer hover:bg-gray-200 text-red-500"
                            x-on:click="openDeleteModal('{{ $attachment->id }}')"
                        >
                            <x-heroicon-s-trash class="w-4 h-4" />
                        </div>
                    </div>
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

    {{-- Livewire modals --}}
    @livewire('laravel-attachments::upload-modal')
    @livewire('laravel-attachments::formatter-modal')
    @livewire('laravel-attachments::edit-modal')
</x-filament::page>
