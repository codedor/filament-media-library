<div
    style="background-image: url('{{ $attachment->url }}')"
    {{ $attributes->except(['slot', 'attachment'])->merge(['class' => '
        flex relative w-full aspect-square rounded-lg overflow-hidden
        bg-center bg-contain bg-no-repeat bg-gray-100 border
    ']) }}
>
    {{ $slot }}
</div>
