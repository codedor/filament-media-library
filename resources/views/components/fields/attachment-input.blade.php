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
    <div
        x-data="{state: $wire.entangle('{{ $getStatePath() }}').defer}"
        style="width: 400px; height: 400px;">
        <button
            type="button"
            class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
            x-on:click="$dispatch('open-modal', {
                id: 'laravel-attachment::upload-attachment-modal'
            })"
        >
            {{ __('laravel_attachment.upload attachment') }}
        </button>

        <button
            type="button"
            class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2rem] px-3 text-sm text-black shadow focus:ring-white bg-white hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
            x-on:click="$dispatch('open-modal', {
                id: 'laravel-attachment::attachment-picker-modal'
            })"
        >
            {{ __('laravel_attachment.select existing media') }}
        </button>

        {{--"$refresh"--}}
        <x-filament::modal
            id="laravel-attachment::attachment-picker-modal"
            width="full"
        >
            @livewire('laravel-attachments::picker')
        </x-filament::modal>

        <x-filament::modal id="laravel-attachment::upload-attachment-modal" width="full">
            @livewire('laravel-attachments::upload-modal')
        </x-filament::modal>
    </div>
</x-dynamic-component>
