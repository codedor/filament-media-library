@php
    $state = $getState();

    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'px-4 py-3 flex flex-col gap-2' => ! $isInline(),
    ]) }}
>
    @if (filled($descriptionAbove))
        <div class="text-sm text-gray-500">
            {{ $descriptionAbove instanceof \Illuminate\Support\HtmlString ? $descriptionAbove : \Illuminate\Support\Str::of($descriptionAbove)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif

    <div class="flex items-center gap-2">
        @if ($state->isEmpty())
            <div class="w-16">
                <div class="
                    flex w-full aspect-square rounded-lg border
                    border-dashed border-gray-300 items-center justify-center
                    overflow-hidden relative
                ">
                    <div
                        class="absolute w-full scale-x-150 bg-gray-100 -rotate-45"
                        style="height: 3px"
                    ></div>
                </div>
            </div>
        @else
            @foreach ($state->take($getLimit()) as $attachment)
                <div class="w-16">
                    <x-filament-media-library::attachment
                        :$attachment
                        :show-title="false"
                        :show-tooltip="false"
                    />
                </div>
            @endforeach

            @if ($state->count() > $getLimit())
                <div class="w-16">
                    <div class="
                        flex relative w-full aspect-square rounded-lg border
                        border-dashed border-gray-300 items-center justify-center
                        text-gray-500
                    ">
                        {{-- TODO: clickable to show all attachments that are linked --}}
                        + {{ $state->count() - $getLimit() }}
                    </div>
                </div>
            @endif
        @endif
    </div>

    @if (filled($descriptionBelow))
        <div class="text-sm text-gray-500">
            {{ $descriptionBelow instanceof \Illuminate\Support\HtmlString ? $descriptionBelow : \Illuminate\Support\Str::of($descriptionBelow)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif
</div>
