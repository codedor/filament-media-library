@props([
    'attachment',
    'containerClass' => null,
    'formats' => [],
    'isDisabled' => false,
    'withDeleteButton' => false,
    'deleteAction' => null,
    'withEditButton' => false,
    'editAction' => null,
    'withCropButton' => false,
    'cropAction' => null,
])

<div @class(['flex flex-col h-full', $containerClass])>
    <div class="flex-grow flex justify-between gap-2">
        {{-- Title --}}
        <p class="font-bold text-sm mb-2">{{ Str::limit($attachment->translated_name, 15) }}.{{ $attachment->extension }}</p>

        {{-- Format tooltip --}}
        @if ($attachment->type === 'image')
            <div>
                <template x-ref="template">
                    <div>
                        <p class="text-sm font-bold">{{ __('laravel_attachment.formats') }}</p>
                        <p class="text-sm">
                            <ul>
                                <li>{{ __('laravel_attachment.original format') }}: {{ $attachment->width }}px x {{ $attachment->height }}px</li>

                                @foreach (($formats) as $format)
                                    <li>{{ $formats->name }}: {{ $formats->width }}px x {{ $formats->height }}px</li>
                                @endforeach
                            </ul>
                        </p>
                    </div>
                </template>

                <button x-tooltip="{
                    content: () => $refs.template.innerHTML,
                    allowHTML: true,
                    appendTo: $root
                }">
                    <x-heroicon-o-information-circle class="h-[1em] text-gray-300" />
                </button>
            </div>
        @endif
    </div>

    {{-- Media --}}
    <div
        @if($attachment->type === 'image')
            style="background-image: url('{{ $attachment->url }}')"
        @endif
        {{ $attributes->except(['slot', 'attachment'])->merge(['class' => '
            flex relative w-full aspect-square rounded-lg overflow-hidden
            bg-center bg-contain bg-no-repeat bg-gray-200 media
        ']) }}
    >
        @if($attachment->type !== 'image')
            <div class="w-full aspect-square flex items-center justify-center bg-gray-100 rounded-lg">
                @if($attachment->type === 'document')
                    <x-heroicon-o-document-text class="w-16 h-16 opacity-50" />
                @elseif($attachment->type === 'video')
                    <x-heroicon-o-video-camera class="w-16 h-16 opacity-50" />
                @else
                    <x-heroicon-o-question-mark-circle class="w-16 h-16 opacity-50" />
                @endif
            </div>
        @endif

        {{-- Buttons --}}
        @unless($isDisabled)
            @if ($withDeleteButton)
                <button
                    x-on:click.prevent="{{ $deleteAction }}"
                    type="button"
                    class="absolute top-1 right-1 bg-white rounded hover:text-red-700 hover:bg-gray-50 shadow-lg"
                >
                    <x-heroicon-o-trash class="p-1 w-6 h-6" />
                </button>
            @endif

            <div class="absolute right-1 bottom-1 left-1 flex justify-end gap-1">
                {{ $slot }}

                @if ($withCropButton && $attachment->type === 'image')
                    {{-- TODO BE: Add cropper modal --}}
                    <button
                        x-on:click.prevent="{{ $cropAction }}"
                        type="button"
                        class=" bg-white rounded hover:text-primary-700 hover:bg-gray-50 shadow-lg"
                    >
                        Crop
                        {{-- TODO: Unable to locate a class or view for component [fas-crop-simple] --}}
                        {{-- <x-fas-crop-simple class="p-1.5 w-6 h-6" /> --}}
                    </button>
                @endif

                @if ($withEditButton)
                    {{-- TODO BE: Add edit modal --}}
                    <button
                        x-on:click.prevent="{{ $editAction }}"
                        type="button"
                        class=" bg-white rounded hover:text-primary-700 hover:bg-gray-50 shadow-lg"
                    >
                        <x-heroicon-s-pencil class="p-1 w-6 h-6" />
                    </button>
                @endif
            </div>
        @endunless
    </div>
</div>
