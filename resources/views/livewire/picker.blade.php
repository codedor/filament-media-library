<div>
    <x-filament::modal
        id="laravel-attachment::attachment-picker-modal-{{ $statePath }}"
        width="6xl"
    >
        <x-filament::modal.heading>
            {{ __('laravel_attachment.select attachment') }}
        </x-filament::modal.heading>

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
                        {{ __('laravel_attachment.reset filters') }}
                    </button>
                @endif
            </div>
        </div>

        <div class="relative" style="aspect-ratio: 2/1">
            @if ($attachments->isEmpty())
                <div class="p-2">
                    {{ __('laravel_attachment.no attachments found') }}
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

                <div class="grid grid-cols-6 gap-2">
                    @foreach($attachments as $attachment)
                        <label
                            for="attachment-{{ $statePath }}-{{ $attachment->id }}"
                            class="block aspect-square relative cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                id="attachment-{{ $statePath }}-{{ $attachment->id }}"
                                key="attachment-{{ $statePath }}-{{ $attachment->id }}"
                                value="{{ $attachment->id }}"

                                @class([
                                    'absolute top-2 left-2 z-10' => $isMultiple,
                                    'hidden' => ! $isMultiple,
                                ])

                                x-on:change.prevent="multiple ? null : selectAttachment('{{ $attachment->id }}')"
                                x-model="selected"
                            >

                            <x-laravel-attachments::attachment :$attachment />
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex justify-between">
            {{ $attachments->links() }}
        </div>

        @if ($isMultiple)
            <x-filament::modal.actions>
                <x-filament::button color="secondary" x-on:click.prevent="closePicker()">
                    {{ __('laravel_attachment.cancel') }}
                </x-filament::button>

                <x-filament::button x-on:click.prevent="selectAttachment()">
                    {{ __('laravel_attachment.select these attachments') }}
                </x-filament::button>
            </x-filament::modal.actions>
        @endif
    </x-filament::modal>
</div>
