<picture {{ $attributes->merge(['class' => '']) }}>
    {{ $slot }}
    <source type="{{ $attachment->mime_type }}" srcset="{{ $attachment->getFormatOrOriginal($format) }}" />
    <img alt="{{ $alt ?? '' }}" src="{{ $attachment->getFormatOrOriginal($format) }}" />
</picture>
