<picture class="{{ $pictureClass }}">
    @foreach ($formats as $breakpoint => $mobileFormat)
        <source
            media="(max-width: {{ $breakpoint ?? '576' }}px)"
            type="image/webp"
            srcset="{{ get_placeholder_url_by_format($mobileFormat) }}"
        >
    @endforeach

    <source
        type="image/webp"
        srcset="{{ get_placeholder_url_by_format($format) }}"
    >

    <img
        alt="{{ $alt }}"
        @class([
            $class ?? 'img-fluid',
            'lazyload' => $lazyload,
        ])
        src="{{ get_placeholder_url_by_format($format) }}"
        width="{{ $width }}"
        height="{{ $height }}"
    >
</picture>
