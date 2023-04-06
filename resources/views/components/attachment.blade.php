<div @class([$containerClass ?? null])>
    <div class="flex justify-between gap-2">
        <p class="font-bold mb-2">{{ $attachment->translated_name }}</p>

        <div>
            <template x-ref="template">
                <div>
                    <p class="text-sm font-bold">{{ __('laravel_attachment.formats') }}</p>
                    <p class="text-sm">
                        <ul>
                            <li>{{ __('laravel_attachment.original format') }}: {{ $attachment->width }}px x {{ $attachment->height }}px</li>
                            @foreach (($formats ?? []) as $format)
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
    </div>

    <div
        style="background-image: url('{{ $attachment->url }}')"
        {{ $attributes->except(['slot', 'attachment'])->merge(['class' => '
            flex relative w-full aspect-square rounded-lg overflow-hidden
            bg-center bg-contain bg-no-repeat bg-gray-200
        ']) }}
    >
        {{ $slot }}
    </div>
</div>
