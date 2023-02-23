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
    <div x-data="{
        state: $wire.entangle(@js($getStatePath())),
        startState: @js($getPickedAttachments()->pluck('id')->toArray()),
        selected: [],
        multiple: @js($isMultiple()),
        pickerModalID: 'laravel-attachment::attachment-picker-modal-{{ $getStatePath() }}',
        search: '',
        openPicker () {
            this.selected = Alpine.raw(this.startState)

            $dispatch('open-modal', { id: this.pickerModalID })
        },
        closePicker () {
            $dispatch('close-modal', { id: this.pickerModalID })
        },
        selectMultiple () {
            this.updateState()
            this.closePicker()
        },
        remove (id) {
            this.selected = this.selected.filter((item) => item !== id)

            this.updateState()
        },
        updateState () {
            this.state = this.multiple ? [...this.selected] : this.selected[0]
            this.startState = Alpine.raw(this.state)
        },
    }">
        @php
            $attachments = $getPickedAttachments();
        @endphp

        <div
            wire:loading.remove
            class="flex flex-col gap-2"
        >
            <div class="flex flex-col gap-2">
                @foreach ($attachments as $attachment)
                    <div class="flex gap-4 p-2 border rounded-lg bg-white">
                        <div
                            style="background-image: url('{{ $attachment->url() }}')"
                            key="attachment-{{ $getStatePath() }}-{{ $attachment->id }}"
                            class="
                                flex relative w-32 aspect-square rounded-lg overflow-hidden
                                bg-center bg-contain bg-no-repeat bg-gray-100 border
                            "
                        >
                            <button
                                x-on:click.prevent="remove('{{ $attachment->id }}')"
                                class="absolute top-2 right-2"
                            >
                                <x-heroicon-o-x class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="flex">
                            {{ $attachment->name }}

                            {{-- TODO: format specifics --}}
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($attachments->isEmpty() || $isMultiple())
                <div class="flex gap-2">
                    <button
                        type="button"
                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
                        x-on:click="$dispatch('open-modal', {
                            id: 'laravel-attachment::upload-attachment-modal-{{ $getStatePath() }}'
                        })"
                    >
                        {{ __('laravel_attachment.upload attachment') }}
                    </button>

                    <button
                        type="button"
                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-black shadow focus:ring-white bg-white hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
                        x-on:click="openPicker()"
                    >
                        {{ __('laravel_attachment.select existing media') }}
                    </button>
                </div>
            @endif
        </div>

        <x-filament::modal
            id="laravel-attachment::attachment-picker-modal-{{ $getStatePath() }}"
            width="full"
        >
            <div>
                {{-- <div>
                    <input wire:model.debounce.500ms="search" type="search" placeholder="{{ __('laravel_attachment.search') }}">
                    <button @click="search = ''">{{ __('laravel_attachment.clear filter') }}</button>
                    filters
                </div> --}}

                <div>
                    @php
                        $attachmentList = $getAttachmentsList();
                    @endphp

                    <div class="grid grid-cols-8 gap-2">
                        @foreach($attachmentList as $attachment)
                            <input
                                id="attachment-{{ $getStatePath() }}-{{ $attachment->id }}"
                                key="attachment-{{ $getStatePath() }}-{{ $attachment->id }}"
                                type="checkbox"
                                x-model="selected"
                                value="{{ $attachment->id }}"
                                class="hidden"
                                x-on:change="multiple ? null : (updateState() || closePicker())"
                            >

                            <label
                                for="attachment-{{ $getStatePath() }}-{{ $attachment->id }}"
                                x-bind:class="{'border': selected.includes(@js($attachment->id))}"
                                class="block"
                            >
                                <img
                                    key="attachment-{{ $getStatePath() }}-{{ $attachment->id }}"
                                    src="{{ $attachment->url() }}"
                                >
                            </label>
                        @endforeach
                    </div>

                    {{-- {{ $attachmentList->links() }} --}}

                    @if ($isMultiple())
                        <div x-on:click="selectMultiple()">
                            {{ __('laravel_attachment.select') }}
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::modal>

        <x-filament::modal
            id="laravel-attachment::upload-attachment-modal-{{ $getStatePath() }}"
            width="full"
        >
            <livewire:laravel-attachments::upload-modal
                wire:key="upload-{{ $getStatePath() }}"
            />
        </x-filament::modal>
    </div>
</x-dynamic-component>
