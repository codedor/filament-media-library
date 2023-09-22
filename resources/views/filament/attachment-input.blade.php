<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $attachments = $getAttachments();
    @endphp

    @if (! $attachments->isEmpty())
        <div
            class="gallery-container flex flex-col gap-4"
            wire:loading.class="opacity-50"
            x-data="{
                state: $wire.entangle(@js($getStatePath())),
                dragging: false,
                reorder (event) {
                    this.dragging = false
                    this.state = event.to.sortable.toArray()
                },
            }"
        >
            <div
                class="gallery"
                @if ($isMultiple() && ! $isDisabled())
                    x-sortable
                    x-on:end="reorder($event)"
                    x-on:start="dragging = true"
                    x-bind:class="dragging ? 'gallery--dragging' : ''"
                @endif
            >
                @foreach($getAttachments() as $attachment)
                    <div
                        class="flex flex-col"
                        x-sortable-handle
                        x-sortable-item="{{ $attachment->id }}"
                        wire:key="attachment-{{ $attachment->id }}-{{ $getStatePath() }}"
                    >
                        <x-filament-media-library::attachment
                            :$attachment
                            :is-disabled="$isDisabled()"
                            :delete-action="$getAction('remove-attachment')"
                            :edit-action="$getAction('edit-attachment')"
                            :format-action="$getAction('format-attachment')"
                            container-class="flex flex-col w-full h-full justify-end transition-opacity"
                            class="
                                relative
                                after:content-['']
                                after:absolute
                                after:inset-0
                                after:opacity-0
                                after:transition-opacity
                                after:bg-gray-100
                                group-hover:after:opacity-80
                            "
                        />
                    </div>
                @endforeach
            </div>
        </div>
   @endif

   @if ($isMultiple() || $attachments->isEmpty())
        <div
            class="flex flex-col gap-2 items-start my-auto hyphens-auto break-words"
            lang="{{ str_replace('_', '-', app()->getLocale()) }}"
        >
            {{ $getAction('attachment-upload') }}
            {{ $getAction('attachment-picker') }}
        </div>
    @endif
</x-dynamic-component>
