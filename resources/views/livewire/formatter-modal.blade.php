<x-filament::modal id="laravel-attachment::formatter-attachment-modal" width="screen">
    @if ($attachment)
        <x-filament::modal.heading>
            {{ __('laravel-attachment.formatter modal heading :name', [
                'name' => $attachment->name,
            ]) }}
        </x-filament::modal.heading>

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
            <div class="w-full flex gap-2">
                <div class="w-3/5">
                    <div style="width: 100%; height: 70vh">
                        <img
                            src="{{ $attachment->url }}"
                            id="laravel-attachments::formatter"
                            wire:key="laravel-attachments::formatter-{{ $attachment->id }}"
                            style="max-width: 100%; max-height: 70vh"
                        >
                    </div>

                    <div class="flex gap-2">
                        <x-filament::button x-on:click.prevent="window.cropper.zoom(-0.1)">
                            {{-- TODO: custom icon, not available in our heroicon version --}}
                            zoom in
                        </x-filament::button>

                        <x-filament::button x-on:click.prevent="window.cropper.zoom(0.1)">
                            {{-- TODO: custom icon, not available in our heroicon version --}}
                            zoom out
                        </x-filament::button>

                        <x-filament::button x-on:click.prevent="window.cropper.rotate(45)">
                            {{-- TODO: custom icon, not available in our heroicon version --}}
                            rotate right
                        </x-filament::button>

                        <x-filament::button x-on:click.prevent="window.cropper.rotate(-45)">
                            {{-- TODO: custom icon, not available in our heroicon version --}}
                            rotate left
                        </x-filament::button>

                        <x-filament::button x-on:click.prevent="window.cropper.scaleX(
                            window.cropper.imageData.scaleX === -1 ? 1 : -1
                        )">
                            {{-- TODO: custom icon, not available in our heroicon version --}}
                            flip x
                        </x-filament::button>

                        <x-filament::button x-on:click.prevent="window.cropper.scaleY(
                            window.cropper.imageData.scaleY === -1 ? 1 : -1
                        )">
                            {{-- TODO: custom icon, not available in our heroicon version --}}
                            flip y
                        </x-filament::button>

                        <x-filament::button x-on:click.prevent="window.cropper.reset()">
                            {{-- TODO: custom icon, not available in our heroicon version --}}
                            reset
                        </x-filament::button>
                    </div>
                </div>

                {{-- Formats --}}
                <div class="w-2/5">
                    {{ $attachment->filename }}<br>

                    <span x-text="currentFormat.name"></span> -
                    <span x-text="currentFormat.width"></span>px x
                    <span x-text="currentFormat.height"></span>px
                    <br>

                    <span x-text="currentFormat.description"></span>

                    <hr>

                    <div class="grid grid-cols-3 gap-4 p-2">
                        <template x-for="(format, key) in formats" :key="key">
                            <div
                                class="flex flex-col gap-2 justify-between rounded-lg bg-gray-200 p-2"
                                x-on:click="setFormat(key)"
                            >
                                <div class="w-full text-center">
                                    <p x-html="format.name"></p>
                                    <p>
                                        <span x-text="format.width"></span> x
                                        <span x-text="format.height"></span>
                                    </p>
                                </div>

                                <div class="flex aspect-video items-center justify-center">
                                    <div
                                        class="bg-gray-400 w-full"
                                        x-bind:style="'aspect-ratio: ' + format.aspectRatio"
                                    ></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <x-filament::button color="secondary" x-on:click.prevent="close() && $wire.set('attachment', null)">
                    {{ __('laravel-attachment.close modal') }}
                </x-filament::button>

                <x-filament::button x-on:click.prevent="submit()">
                    {{ __('laravel-attachment.save format') }}
                </x-filament::button>
            </div>
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
