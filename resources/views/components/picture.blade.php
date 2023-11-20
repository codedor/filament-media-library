@if ($placeholder)
    <x-filament-media-library::placeholder
        :format="$format"
        :formats="$formats"
        :picture-class="$pictureClass"
        :class="$class"
        :alt="$alt"
    />
@elseif ($format && ! $formats)
    <picture class="{{ $pictureClass }}">
        @if (method_exists($image, 'getWebpFormatOrOriginal') && $image->getWebpFormatOrOriginal($format))
            <source
                type="image/webp"
                srcset="{{ $image->getWebpFormatOrOriginal($format) }}"
            >
        @endif
        <img
            alt="{{ $alt }}"
            title="{{ $title }}"
            @class([
                $class ?? 'img-fluid',
                'lazyload' => $lazyload,
            ])
            src="{{ $image->getFormatOrOriginal($format) }}"

            @if (! empty($width()))
                width="{{ $width() }}"
            @endif

            @if (! empty($height()))
                height="{{ $height() }}"
            @endif
        >
    </picture>
@elseif ($image)
    <picture class="{{ $pictureClass }}">
        @if (method_exists($image, 'getWebpFormatOrOriginal') && $image->getWebpFormatOrOriginal($format))
            @foreach ($formats as $breakpoint => $mobileFormat)
                <source
                    media="(max-width: {{ $breakpoint ?? '576' }}px)"
                    type="image/webp"
                    srcset="{{ $image->getWebpFormatOrOriginal($mobileFormat) }}"
                >
            @endforeach
            <source
                type="image/webp"
                srcset="{{ $image->getWebpFormatOrOriginal($format) }}"
            >
        @else
            @foreach ($formats as $breakpoint => $mobileFormat)
                <source
                    media="(max-width: {{ $breakpoint ?? '576' }}px)"
                    type="image/webp"
                    srcset="{{ $image->getFormatOrOriginal($mobileFormat) }}"
                >
            @endforeach
            <source
                type="image/webp"
                srcset="{{ $image->getFormatOrOriginal($format) }}"
            >
        @endif
        <img
            alt="{{ $alt }}"
            title="{{ $title }}"
            @class([
                $class ?? 'img-fluid',
                'lazyload' => $lazyload,
            ])
            src="{{ $image->getFormatOrOriginal($format) }}"

            @if (! empty($width()))
                width="{{ $width() }}"
            @endif

            @if (! empty($height()))
                height="{{ $height() }}"
            @endif
        >
    </picture>
@endif
