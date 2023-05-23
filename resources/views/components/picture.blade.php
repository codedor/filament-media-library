@props([
    'placeholder' => false,
    'image' => null,
    'format' => '',
    'formats' => [],
    'pictureClass' => '',
    'class' => '',
    'alt' => '',
    'title' => '',
    'lazyload' => true,
    // TODO BE: get the width and height from the format
    'width' => '',
    'height' => '',
])

@if ($placeholder)
    <x-laravel-attachments::placeholder-picture
        :format="$format"
        :formats="$formats"
        :picture-class="$pictureClass"
        :class="$class"
        :alt="$alt"
    />
@elseif ($image)
    <picture class="{{ $pictureClass }}">
        @if ($image->getWebpFormatOrOriginal($format))
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
            width="{{ $width }}"
            height="{{ $height }}"
        >
    </picture>
@endif
