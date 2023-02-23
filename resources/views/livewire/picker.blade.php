<div x-data="{
    selected: $wire.entangle('selectedAttachments').defer,
    multiple: @js($multiple),
    toggle (id) {
        if (this.selected.includes(id)) {
            this.selected = this.selected.filter((item) => item !== id)
        } else {
            this.selected.push(id)
        }

        if (! this.multiple) {
            this.selected = [id]

            $wire.selectAttachments()

            $dispatch('close-modal', {
                id: 'laravel-attachment::attachment-picker-modal-{{ $statePath }}'
            })
        }
    },
}">
    <div>
        <input wire:model.debounce.500ms="search" type="search" placeholder="{{ __('laravel_attachment.search') }}">
        <button @click="search = ''">{{ __('laravel_attachment.clear filter') }}</button>
        filters
    </div>

    <div>
        @foreach($attachments as $attachment)
            <div
                x-on:click="toggle(@js($attachment->id))"
                x-bind:class="{'text-primary-500': selected.includes(@js($attachment->id))}"
            >
                {{ $attachment->filename() }}
            </div>

        @endforeach

        {{ $attachments->links() }}

        <template x-if="multiple">
            <div x-on:click.prevent="$wire.selectAttachments()">
                {{ __('laravel_attachment.select') }}
            </div>
        </template>
    </div>
</div>
