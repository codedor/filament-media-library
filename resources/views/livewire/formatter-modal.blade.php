<x-filament::modal id="laravel-attachment::formatter-attachment-modal" width="full">
    @if ($attachment)
        <x-filament::modal.heading>
            {{ __('laravel-attachment.formatter modal heading :name', [
                'name' => $attachment->name,
            ]) }}
        </x-filament::modal.heading>

        <div class="py-8 w-full flex gap-2" wire:loading.remove>
            {{-- Actual formatter --}}
            <div class="w-3/5">
                <div x-data="{
                    init () {
                        this.loadFormatter()
                        window.addEventListener('laravel-attachments::load-formatter', (e) => this.loadFormatter(e))
                    },
                    loadFormatter (e = {}) {
                        window.cropper = new Cropper(document.getElementById('laravel-attachments::formatter'), {
                            aspectRatio: @js($currentFormat->aspectRatio()),
                            viewMode: 1,
                            dragMode: 'move',
                        })
                    },
                }">
                    {{-- <select name="" id="">
                        @foreach ($formats as $format)
                            <option value="{{ $format['key'] }}">
                                {{ $format['name'] }}
                            </option>
                        @endforeach
                    </select> --}}

                    <img
                        src="{{ $attachment->url }}"
                        id="laravel-attachments::formatter"
                        wire:key="laravel-attachments::formatter-{{ $attachment->id }}"
                    >
                </div>
            </div>

            {{-- Formats --}}
            <div class="w-2/5">
                {{ $attachment->filename }}<br>

                {{ $currentFormat->name() }} -
                {{ $currentFormat->width() }}px x
                {{ $currentFormat->height() }}px
                <br>

                {{ $currentFormat->description() }}

                <hr>

                <div class="grid grid-cols-3 gap-4 p-2">
                    @foreach ($formats as $format)
                        <div class="flex flex-col gap-2 justify-between rounded-lg bg-gray-200 p-2">
                            <div class="w-full text-center">
                                <p>{{ $format->name() }}</p>
                                <p>{{ $format->width() }} x {{ $format->height() }}</p>
                            </div>

                            <div class="flex aspect-video items-center justify-center">
                                <div
                                    class="bg-gray-400 w-full"
                                    style="aspect-ratio: {{ $format->width() }}/ {{ $format->height() }}"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="w-full justify-center py-8" wire:loading.flex>
            <x-filament-support::loading-indicator class="w-10 h-10" />
        </div>

        <x-filament::modal.actions>
            <x-filament::button color="secondary" x-on:click.prevent="close() && $wire.set('attachment', null)">
                {{ __('laravel-attachment.close modal') }}
            </x-filament::button>

            <x-filament::button x-on:click.prevent="$wire.submit()">
                {{ __('laravel-attachment.save format') }}
            </x-filament::button>
        </x-filament::modal.actions>
    @else
        <div class="w-full flex justify-center py-8">
            <x-filament-support::loading-indicator class="w-10 h-10" />
        </div>
    @endif
</x-filament::modal>
