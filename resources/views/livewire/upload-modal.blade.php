<x-filament::modal id="laravel-attachment::upload-attachment-modal" width="full">
    {{ $this->form }}

    <button wire:click.prevent="submit" type="submit">
        Submit
    </button>
</x-filament::modal>
