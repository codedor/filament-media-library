<label for="resource-picker::{{ $statePath }}-{{ $item->{$keyField} }}">
    <div style="display: none">
        {{ $slot }}
    </div>

    <div :class="{
        'transition-all cursor-pointer': true,
        'rounded-lg p-1 border-2 border-primary-600': state.includes('{{ $item->{$keyField} }}')
    }">
        <x-filament-media-library::attachment :attachment="$item" />
    </div>
</label>
