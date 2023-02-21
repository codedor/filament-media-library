<div>
    <div>
        <input wire:model.debounce.500ms="search" type="search" placeholder="{{ __('laravel_attachment.search') }}">
        <button @click="search = ''">{{ __('laravel_attachment.clear filter') }}</button>
        filters
    </div>

    <div>
        @foreach($attachments as $attachment)
            <div
                wire:click="toggle('{{ $attachment->id }}')"
                @class([
                    'text-orange-700' => in_array($attachment->id, $selectedAttachments),
                ])
            >
                {{ $attachment->filename() }}
            </div>

        @endforeach

        {{ $attachments->links() }}
    </div>
</div>
