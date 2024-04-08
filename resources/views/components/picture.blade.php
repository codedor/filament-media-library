@if ($placeholder)
    <x-filament-media-library::placeholder
        :$format
        :$formats
        :$pictureClass
        :$class
        :$alt
    />
@elseif ($image && $format)
    <picture class="{{ $pictureClass }}" x-data="{ intersected: false }">
        @if ($formats)
            @foreach ($formats as $breakpoint => $mobileFormat)
                @if ($hasWebp)
                    <source
                        media="(max-width: {{ $breakpoint ?? '576' }}px)"
                        type="image/webp"
                        srcset="{{ $image->getWebpFormatOrOriginal($mobileFormat) }}"
                    >
                @endif

                <source
                    media="(max-width: {{ $breakpoint ?? '576' }}px)"
                    type="{{ $image->mime_type }}"
                    srcset="{{ $image->getFormatOrOriginal($mobileFormat) }}"
                >
            @endforeach
        @endif

        @if ($hasWebp)
            <source
                type="image/webp"
                @if ($lazyload)
                    srcset="{{ $image->getWebpFormatOrOriginal($lazyloadInitialFormat) }}"
                    data-srcset="{{ $image->getWebpFormatOrOriginal($format) }}"
                    x-intersect.{{ $intersectModifier }}="$el.srcset = $el.dataset.srcset; intersected = true"
                @else
                    srcset="{{ $image->getWebpFormatOrOriginal($format) }}"
                @endif
            >
        @endif

        <img
            alt="{{ $alt }}"
            title="{{ $title }}"
            @class([
                $class ?? 'img-fluid',
                'lazyload' => $lazyload,
            ])
            @if ($lazyload)
                src="{{ $image->getFormatOrOriginal($lazyloadInitialFormat) }}"
                data-src="{{ $image->getFormatOrOriginal($format) }}"
                x-intersect.{{ $intersectModifier }}="$el.src = $el.dataset.src"
                x-bind:class="{ 'lazyloaded': intersected }"
            @else
                src="{{ $image->getFormatOrOriginal($format) }}"
            @endif

            @if (! empty($width()))
                width="{{ $width() }}"
            @endif

            @if (! empty($height()))
                height="{{ $height() }}"
            @endif
        >
    </picture>
@endif
