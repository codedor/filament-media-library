<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    @php
        $attachments = $getPickedAttachments();
    @endphp

    <div x-data="{
        state: $wire.entangle(@js($getStatePath())).defer,
        initial: @js($attachments->pluck('id')->toArray()),
        multiple: @js($isMultiple()),
        pickerModalID: 'laravel-attachment::attachment-picker-modal-{{ $getStatePath() }}',
        init () {
            this.state = [...this.initial]

            window.addEventListener('laravel-attachment::uploaded-images', (event) => {
                if (event.detail.statePath !== '{{ $getStatePath() }}') {
                    return
                }

                this.state = [...this.state, ...event.detail.attachments]
                this.updateState()
            })

            window.addEventListener('laravel-attachment::picked-attachments', (event) => {
                if (event.detail.statePath !== '{{ $getStatePath() }}') {
                    return
                }

                this.state = [...event.detail.attachments]
                this.updateState()
            })
        },
        openPicker () {
            $dispatch('open-modal', { id: this.pickerModalID })

            $wire.emit('laravel-attachments::open-picker', {
                statePath: '{{ $getStatePath() }}',
                attachments: this.state || this.state || [],
            })
        },
        closePicker () {
            $dispatch('close-modal', { id: this.pickerModalID })
        },
        remove (id) {
            if (! this.multiple) {
                this.state = []
            } else {
                this.state = [...this.state.filter((item) => item !== id)]
            }

            this.updateState()
        },
        updateState () {
            $wire.$refresh()
        },
        reorder (event) {
            const state = Alpine.raw(this.state)
            const reorderedRow = state.splice(event.oldIndex, 1)[0]

            state.splice(event.newIndex, 0, reorderedRow)
            this.state = [...state]
        }
    }">
        <div class="flex flex-col gap-4" wire:loading.class="opacity-50">
            <div
                class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
                @if ($isMultiple() && ! $isDisabled())
                    x-sortable
                    x-on:end="reorder($event)"
                @endif
            >
                @foreach ($attachments as $attachment)
                    <div
                        class="flex flex-col"
                        x-sortable-item="{{ $attachment->id }}"
                        wire:key="attachment-{{ $attachment->id }}-{{ $getStatePath() }}"
                    >
                        <div
                            x-sortable-handle
                            @class([
                                'cursor-move' => ($isMultiple() && ! $isDisabled()),
                                'flex-1 flex gap-2 items-center justify-center text-gray-900',
                            ])
                        >
                            @if ($isMultiple() && ! $isDisabled())
                                <x-heroicon-o-selector class="w-5 h-5" />
                            @endif

                            <x-laravel-attachments::attachment
                                :$attachment
                                :is-disabled="$isDisabled()"
                                container-class="flex flex-col w-full h-full justify-end"
                                :with-delete-button="true"
                                delete-action="remove('{{ $attachment->id }}')"
                                {{-- TODO BE: Add formats used in this module --}}
                                {{-- :formats="[['name' => 'test', 'width' => 100, 'height' => 100]]" --}}
                            >
                                {{-- @unless($isDisabled()) --}}
                                    {{-- <button
                                        x-on:click.prevent="remove('{{ $attachment->id }}')"
                                        class="absolute top-1 right-1 bg-white rounded hover:text-red-700 hover:bg-gray-50 shadow-lg"
                                    >
                                        <x-heroicon-o-trash class="p-1 w-6 h-6" />
                                    </button> --}}

                                    {{-- <div class="absolute right-1 bottom-1 left-1 flex justify-end gap-1"> --}}
                                        {{-- TODO BE: Add cropper modal --}}
                                        {{-- <button
                                            class=" bg-white rounded hover:text-primary-700 hover:bg-gray-50 shadow-lg"
                                        >
                                            Crop --}}
                                            {{-- TODO: Unable to locate a class or view for component [fas-crop-simple] --}}
                                            {{-- <x-fas-crop-simple class="p-1.5 w-6 h-6" /> --}}
                                        {{-- </button> --}}

                                        {{-- TODO BE: Add edit modal --}}
                                        {{-- <button
                                            class=" bg-white rounded hover:text-primary-700 hover:bg-gray-50 shadow-lg"
                                        >
                                            <x-heroicon-s-pencil class="p-1 w-6 h-6" />
                                        </button> --}}
                                    {{-- </div> --}}
                                {{-- @endunless --}}
                            </x-laravel-attachments::attachment>
                        </div>
                    </div>
                @endforeach
            </div>

            @if (! $isDisabled() && ($attachments->isEmpty() || $isMultiple()))
                <div class="flex flex-col gap-2 items-start">
                    <button
                        type="button"
                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
                        x-on:click.prevent="$dispatch('open-modal', {
                            id: 'laravel-attachment::upload-attachment-modal-{{ $getStatePath() }}'
                        })"
                    >
                        {{ __('laravel_attachment.upload attachment') }}
                    </button>

                    <button
                        type="button"
                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 filament-page-button-action"
                        x-on:click.prevent="openPicker()"
                    >
                        {{ __('laravel_attachment.select existing media') }}
                    </button>
                </div>
            @endif
        </div>

        @unless ($isDisabled())
            <livewire:laravel-attachments::picker
                wire:key="picker-{{ $getStatePath() }}"
                :state-path="$getStatePath()"
                :attachments-list="$getAttachmentsList()->pluck('id')->toArray()"
                :is-multiple="$isMultiple()"
            />

            <x-filament::modal
                id="laravel-attachment::upload-attachment-modal-{{ $getStatePath() }}"
                width="3xl"
            >
                <livewire:laravel-attachments::upload-modal
                    wire:key="upload-{{ $getStatePath() }}"
                    :state-path="$getStatePath()"
                />
            </x-filament::modal>
        @endunless
    </div>
</x-dynamic-component>
