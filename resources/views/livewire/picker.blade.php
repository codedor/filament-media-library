<div x-data="{
    selected: $wire.entangle('selected').defer,
    selectAttachment (id = null) {
        // Only one attachment can be selected if not multiple
        id ? this.selected = [id] : null

        window.dispatchEvent(new CustomEvent('laravel-attachment::picked-attachments', {
            detail: {
                statePath: '{{ $statePath }}',
                attachments: this.selected,
            }
        }))

        this.closePicker()
    },
    closePicker () {
        $dispatch('close-modal', {
            id: 'laravel-attachment::attachment-picker-modal-{{ $statePath }}'
        })
    },
}">
    <x-filament::modal
        id="laravel-attachment::attachment-picker-modal-{{ $statePath }}"
        width="6xl"
    >
        <x-slot name="header">
            <x-filament::modal.heading>
                {{ __('filament_media.select attachment') }}
            </x-filament::modal.heading>
        </x-slot>

        <div class="flex gap-2">
            <div class="relative w-2/4 z-20">
                {{ $this->form }}
            </div>

            <div class="w-2/4 flex items-center justify-end">
                @if (filled($filters['query']) || filled($filters['tags']))
                    <button
                        class="inline-flex gap-2 items-center px-2 py-1"
                        wire:click.prevent="resetFilters()"
                    >
                        <x-heroicon-o-x class="w-5 h-5" />
                        {{ __('filament_media.reset filters') }}
                    </button>
                @endif
            </div>
        </div>

        <div @class(['relative gallery-container', '!mt-8' => $isMultiple])>
            @if ($attachments->isEmpty())
                <div class="p-2">
                    {{ __('filament_media.no attachments found') }}
                </div>
            @else
                <div
                    wire:loading.flex
                    class="absolute inset-0 z-50 rounded-lg items-center justify-center bg-white"
                >
                    <x-filament-support::loading-indicator
                        class="w-10 h-10"
                    />
                </div>

                <div class="gallery gap-y-6">
                    @foreach($attachments as $attachment)
                        <label
                            for="attachment-{{ $statePath }}-{{ $attachment->id }}"
                            class="block relative cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                id="attachment-{{ $statePath }}-{{ $attachment->id }}"
                                key="attachment-{{ $statePath }}-{{ $attachment->id }}"
                                value="{{ $attachment->id }}"

                                @class([
                                    'absolute bottom-3 left-3 z-10 peer' => $isMultiple,
                                    'hidden' => ! $isMultiple,
                                ])

                                x-on:change.prevent="multiple ? null : selectAttachment('{{ $attachment->id }}')"
                                x-model="selected"
                            >

                            <x-laravel-attachments::attachment
                                :$attachment
                                container-class="rounded-lg transition-all peer-checked:p-2 peer-checked:bg-gray-200 peer-checked:[&_.media]:scale-90 peer-checked:[&_.attachment-tooltip]:text-gray-400"
                                class="transition-transform"
                                :extendedTooltip="true"
                            />
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="!mt-5">
            {{ $attachments->links() }}
        </div>

        @if ($isMultiple)
            <x-slot name="footer">
                <x-filament::modal.actions>
                    <x-filament::button color="secondary" x-on:click.prevent="closePicker()">
                        {{ __('filament_media.cancel') }}
                    </x-filament::button>

                    <x-filament::button x-on:click.prevent="selectAttachment()">
                        {{ __('filament_media.select these attachments') }}
                    </x-filament::button>
                </x-filament::modal.actions>
            </x-slot>
        @endif
    </x-filament::modal>
</div>
