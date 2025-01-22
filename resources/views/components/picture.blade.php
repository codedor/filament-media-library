@if ($placeholder)
    <x-filament-media-library::placeholder
        :$format
        :$formats
        :$pictureClass
        :$class
        :$alt
    />
@elseif ($image && $format)
    <div
        @class([
            'image-container',
            'image-container--lazyload' => $lazyload,
            $containerClass,
        ])
        @style([
            '--lazyload-image: url(' . $image->getFormatOrOriginal($lazyloadInitialFormat) . ')' => $lazyload,
        ])
        @if ($lazyload)
            data-image-container-lazyload
        @endif
    >
        <picture class="{{ $pictureClass }}">
            @if ($formats)
                @foreach ($formats as $breakpoint => $mobileFormat)
                    @if ($hasWebp)
                        <source
                            media="(max-width: {{ $breakpoint ?? '576' }}px)"
                            type="image/webp"
                            srcset="{{ $image->getWebpFormatOrOriginal($mobileFormat) }}"
                            width={{ $width($mobileFormat) }}
                            height={{ $height($mobileFormat) }}
                        >
                    @endif

                    <source
                        media="(max-width: {{ $breakpoint ?? '576' }}px)"
                        type="{{ $image->mime_type }}"
                        srcset="{{ $image->getFormatOrOriginal($mobileFormat) }}"
                        width={{ $width($mobileFormat) }}
                        height={{ $height($mobileFormat) }}
                    >
                @endforeach
            @endif

            @if ($hasWebp)
                <source
                    type="image/webp"
                    srcset="{{ $image->getWebpFormatOrOriginal($format) }}"
                >
            @endif

            <img
                alt="{{ $alt }}"
                title="{{ $title }}"

                @class([
                    'image',
                    $class ?? 'img-fluid',
                ])

                @if ($lazyload)
                    loading="lazy"
                @endif

                src="{{ $image->getFormatOrOriginal($format) }}"
                width={{ $width() }}
                height={{ $height() }}
            >
        </picture>
    </div>
@endif
