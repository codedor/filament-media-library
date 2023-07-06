@php
    $attachment = $getRecord();
@endphp

<div
    @if ($attachment->type === 'image')
        style="background-image: url('{{ $attachment->url }}')"
    @endif
    class="
        attachment-visual flex relative w-full aspect-square rounded-lg
        overflow-hidden bg-center bg-contain bg-no-repeat bg-gray-200 media mt-4
    "
>
    @if ($attachment->type !== 'image')
        <div class="w-full aspect-square flex items-center justify-center bg-gray-100 rounded-lg">
            @if($attachment->type === 'document')
                <x-heroicon-o-document-text class="w-16 h-16 opacity-50"/>
            @elseif($attachment->type === 'video')
                <x-heroicon-o-video-camera class="w-16 h-16 opacity-50"/>
            @else
                <x-heroicon-o-question-mark-circle class="w-16 h-16 opacity-50"/>
            @endif
        </div>
    @endif
</div>
