@props([
    'attachment',
    'containerClass' => null,
    'formats' => [],
    'isDisabled' => false,
    'deleteAction' => null,
    'deleteButtonTitle' => __('laravel-attachment.delete attachment'),
    'editAction' => null,
    'formatterAction' => null,
    'extendedTooltip' => false,
    'showTitle' => true,
    'showTooltip' => true
])

<div
    @class(['flex flex-col h-full', $containerClass])
    x-data="{
        openFormatterModal (id) {
            $dispatch('open-modal', { id: 'laravel-attachment::formatter-attachment-modal' })
            $wire.emit('laravel-attachment::open-formatter-attachment-modal', id)
        },
        openEditModal (id) {
            $dispatch('open-modal', { id: 'laravel-attachment::edit-attachment-modal' })
            $wire.emit('laravel-attachment::open-edit-attachment-modal', id)
        },
        closeEditModal () {
            $wire.emit('laravel-attachment::close-edit-attachment-modal')
        }
    }"
>
    <div class="flex-grow flex justify-between gap-2">
        {{-- Title --}}
        @if ($showTitle)
            <p class="font-bold text-sm mb-2 line-clamp-1 border-primary-600">
                {{ $attachment->filename }}
            </p>
        @endif

        {{-- Format tooltip --}}
        @if ($showTooltip && ($attachment->type === 'image' || $extendedTooltip))
            <div>
                <template x-ref="attachment-tooltip-{{ $attachment->id }}">
                    @if ($attachment->type === 'image' && !$extendedTooltip)
                        <div>
                            <p class="text-sm font-bold">{{ __('laravel_attachment.formats') }}</p>
                            <p class="text-sm">
                            <ul>
                                <li>{{ __('laravel_attachment.original format') }}: {{ $attachment->width }}px x {{ $attachment->height }}px</li>

                                @foreach ($formats as $format)
                                    <li>{{ $format->name }}: {{ $format->width }}px x {{ $format->height }}px</li>
                                @endforeach
                            </ul>
                            </p>
                        </div>
                    @else
                        <div>
                            <dl>
                                <dt class="text-sm font-bold">{{ __('laravel_attachment.filename') }}</dt>
                                <dd class="mb-2 text-sm">{{ $attachment->filename }}</dd>

                                <dt class="text-sm font-bold">{{ __('laravel_attachment.type') }}</dt>
                                <dd class="mb-2 text-sm">{{ $attachment->type }}</dd>

                                <dt class="text-sm font-bold">{{ __('laravel_attachment.tags') }}</dt>
                                <dd class="mb-2 text-sm">{{ $attachment->tags->count() ? $attachment->tags->implode('title', ', ') : __('laravel_attachment.no tags for this attachment') }}</dd>

                                {{-- TODO BE: Format size --}}
                                <dt class="text-sm font-bold">{{ __('laravel_attachment.size') }}</dt>
                                <dd class="mb-2 text-sm">{{ $attachment->size }}</dd>

                                {{-- TODO BE: Format date --}}
                                <dt class="text-sm font-bold">{{ __('laravel_attachment.created at') }}</dt>
                                <dd class="mb-2 text-sm">{{ $attachment->created_at }}</dd>

                                {{-- TODO BE: Format date --}}
                                <dt class="text-sm font-bold">{{ __('laravel_attachment.updated at') }}</dt>
                                <dd class="mb-2 text-sm">{{ $attachment->updated_at }}</dd>

                                <dt class="text-sm font-bold">{{ __('laravel_attachment.original format') }}</dt>
                                <dd class="mb-2 text-sm">{{ $attachment->width }}px x {{ $attachment->height }}px</dd>
                            </dl>
                        </div>
                    @endif
                </template>

                <button x-tooltip="{
                    content: () => $refs['attachment-tooltip-{{ $attachment->id }}'].innerHTML,
                    allowHTML: true,
                    appendTo: $root
                }">
                    <x-heroicon-o-information-circle class="h-[1em] text-gray-300 attachment-tooltip"/>
                </button>
            </div>
        @endif
    </div>

    {{-- Media --}}
    <div
        @if($attachment->type === 'image')
            style="background-image: url('{{ $attachment->url }}')"
        @endif

        {{ $attributes->except(['slot', 'attachment'])->merge(['class' => '
            attachment-visual flex relative w-full aspect-square rounded-lg
            overflow-hidden bg-center bg-contain bg-no-repeat bg-gray-200 media
        ']) }}
    >
        @if($attachment->type !== 'image')
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

        {{-- Buttons --}}
        @unless($isDisabled)
            <div class="absolute right-1 bottom-1 left-1 z-10 flex justify-between gap-3">
                @if ($deleteAction)
                    <button
                        x-on:click.prevent="{{ $deleteAction }}"
                        type="button"
                        class="bg-white rounded text-red-700 hover:text-white hover:bg-red-700 shadow-lg"
                        title="{{ $deleteButtonTitle }}"
                    >
                        <x-heroicon-o-trash class="p-1 w-6 h-6"/>
                    </button>
                @endif

                <div class=" flex justify-end gap-1">
                    {{ $slot }}

                    @if ($formatterAction && $attachment->type === 'image')
                        <button
                            x-on:click.prevent="{{ $formatterAction }}"
                            type="button"
                            class=" bg-white rounded hover:text-primary-700 hover:bg-gray-50 shadow-lg"
                            title="{{ __('laravel-attachment.format attachment') }}"
                        >
                            <x-attachments-crop-regular class="p-1 w-6 h-6"/>
                        </button>
                    @endif

                    @if ($editAction)
                        {{-- TODO BE: Add edit modal --}}
                        <button
                            x-on:click.prevent="{{ $editAction }}"
                            type="button"
                            class=" bg-white rounded hover:text-primary-700 hover:bg-gray-50 shadow-lg"
                            title="{{ __('laravel-attachment.edit attachment') }}"
                        >
                            <x-heroicon-s-pencil class="p-1 w-6 h-6"/>
                        </button>
                    @endif
                </div>
            </div>
        @endunless
    </div>
</div>
