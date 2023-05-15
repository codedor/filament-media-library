<x-filament::modal
    id="laravel-attachment::formatter-attachment-modal"
    width="w-6xl"
>
    @if ($attachment)
        <x-slot name="header">
            <x-filament::modal.heading>
                {{ __('filament_media.formatter modal heading :name', [
                    'name' => $attachment->name,
                ]) }}
            </x-filament::modal.heading>
        </x-slot>

        <div
            class="py-8 w-full flex-col gap-2"
            wire:loading.remove
            wire:key="laravel-attachments::formatter-attachment-modal-{{ $attachment->id }}"
            x-data="{
                formats: @js($formats),
                previousFormats: @js($previousFormats),
                currentFormat: null,
                init () {
                    this.currentFormat = window.currentFormat
                        || Object.values(this.formats)[0]
                        || null

                    this.loadFormatter()
                    window.addEventListener('laravel-attachments::load-formatter', () => this.loadFormatter())
                    window.addEventListener('laravel-attachments::submit-formatter', () => this.submit())
                },
                loadFormatter () {
                    if (! this.currentFormat) {
                        return
                    }

                    if (typeof window.cropper === 'object') {
                        window.cropper.destroy()
                    }

                    let previousData = this.previousFormats[this.currentFormat.key] || {}

                    window.cropper = new Cropper(document.getElementById('laravel-attachments::formatter'), {
                        viewMode: 1,
                        dragMode: 'move',
                        aspectRatio: this.currentFormat.aspectRatio,
                        ready() {
                            if (previousData) {
                                window.cropper.setData(previousData)
                            }
                        },
                    })
                },
                submit () {
                    this.previousFormats[this.currentFormat.key] = window.cropper.getData()
                    window.currentFormat = this.currentFormat

                    $wire.emit('cropped', {
                        crop: this.getCroppedCanvas().toDataURL('{{ $attachment->mime_type }}'),
                        format: this.currentFormat,
                        data: window.cropper.getData(),
                    })
                },
                getCroppedCanvas () {
                    return window.cropper.getCroppedCanvas({
                        width: this.currentFormat.width,
                        height: this.currentFormat.height
                    })
                },
                setFormat (key) {
                    this.currentFormat = this.formats[key] || null

                    if (this.currentFormat) {
                        window.cropper.setAspectRatio(this.currentFormat.aspectRatio)
                        window.cropper.setData(this.previousFormats[this.currentFormat.key] || {})
                    }
                }
            }"
        >
            {{-- Actual formatter --}}
            <div class="w-full flex flex-col lg:flex-row gap-6">
                <div class="lg:w-4/6 order-last lg:order-first">
                    <div class="w-full h-[70vh]">
                        <img
                            src="{{ $attachment->url }}"
                            id="laravel-attachments::formatter"
                            wire:key="laravel-attachments::formatter-{{ $attachment->id }}"
                            style="max-width: 100%; max-height: 70vh"
                        >
                    </div>

                    <div class="flex gap-4 flex-wrap justify-center mt-2">
                        <div class="flex gap-1">
                            <x-filament::button
                                x-on:click.prevent="window.cropper.zoom(-0.1)"
                                title="{{ __('filament_media.zoom in') }}"
                            >
                                <x-heroicon-o-zoom-in class="h-4" />
                            </x-filament::button>

                            <x-filament::button
                                x-on:click.prevent="window.cropper.zoom(0.1)"
                                title="{{ __('filament_media.zoom out') }}"
                            >
                                <x-heroicon-o-zoom-out class="h-4" />
                            </x-filament::button>
                        </div>

                        <div class="flex gap-1">
                            <x-filament::button
                                x-on:click.prevent="window.cropper.rotate(45)"
                                title="{{ __('filament_media.rotate 45 degrees clockwise') }}"
                            >
                                {{-- <x-fas-rotate-right class="h-4" /> --}}
                            </x-filament::button>

                            <x-filament::button
                                x-on:click.prevent="window.cropper.rotate(-45)"
                                title="{{ __('filament_media.rotate 45 degrees counterclockwise') }}"
                            >
                                {{-- <x-fas-rotate-left class="h-4" /> --}}
                            </x-filament::button>
                        </div>

                        <div class="flex gap-1">
                            <x-filament::button x-on:click.prevent="window.cropper.scaleX(
                                window.cropper.imageData.scaleX === -1 ? 1 : -1
                            )" title="{{ __('filament_media.flip horizontally') }}">
                                <x-attachments-flip-horizontal class="h-4" />
                            </x-filament::button>

                            <x-filament::button x-on:click.prevent="window.cropper.scaleY(
                                window.cropper.imageData.scaleY === -1 ? 1 : -1
                            )" title="{{ __('filament_media.flip vertically') }}">
                                <x-attachments-flip-vertical class="h-4" />
                            </x-filament::button>
                        </div>

                        <x-filament::button x-on:click.prevent="window.cropper.reset()">
                            {{ __('filament_media.reset format') }}
                        </x-filament::button>
                    </div>
                </div>

                {{-- Formats --}}
                <div class="lg:w-2/6 gallery-container lg:h-[70vh] flex flex-col">
                    <h3 class="text-lg font-bold mb-2">{{ $attachment->filename }}</h3>

                    <p class="mb-1">
                        <span x-text="currentFormat.name" class="font-bold"></span> -
                        <span x-text="currentFormat.width"></span>px x
                        <span x-text="currentFormat.height"></span>px
                    </p>

                    <p x-text="currentFormat.description"></p>

                    <hr class="my-6">

                    <div class="max-lg:flex max-md:h-40 max-lg:h-52 gallery overflow-x-auto lg:overflow-y-auto">
                        <template x-for="(format, key) in formats" :key="key">
                            <div
                                class="flex-shrink-0 max-lg:aspect-[3/4] flex flex-col gap-2 justify-between rounded-lg bg-gray-200 p-2"
                                :class="{'ring-2 ring-primary-500 ring-inset': currentFormat.key === format.key}"
                                x-on:click="setFormat(key)"
                            >
                                <div class="w-full text-center">
                                    <p x-html="format.name"></p>
                                    <p>
                                        <span x-text="format.width"></span> x
                                        <span x-text="format.height"></span>
                                    </p>
                                </div>

                                <div class="flex aspect-square items-center justify-center">
                                    <div
                                        class="bg-gray-400"
                                        :class="format.aspectRatio > 1 ? 'w-full' : 'h-full'"
                                        x-bind:style="'aspect-ratio: ' + format.aspectRatio"
                                    ></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <x-slot name="footer">
                <x-filament::modal.actions>
                    <x-filament::button x-on:click.prevent="window.dispatchEvent(new Event('laravel-attachments::submit-formatter'))">
                        {{ __('filament_media.save format') }}
                    </x-filament::button>

                    <x-filament::button color="secondary" x-on:click.prevent="close() && $wire.set('attachment', null)">
                        {{ __('filament_media.close modal') }}
                    </x-filament::button>
                </x-filament::modal.actions>
            </x-slot>
        </div>

        <div class="w-full justify-center py-8" wire:loading.flex>
            <x-filament-support::loading-indicator class="w-10 h-10" />
        </div>
    @else
        <div class="w-full flex justify-center py-8">
            <x-filament-support::loading-indicator class="w-10 h-10" />
        </div>
    @endif
</x-filament::modal>
