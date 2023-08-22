<label for="resource-picker::{{ $statePath }}-{{ $item->{$keyField} }}">
    <div style="display: none">
        {{ $slot }}
    </div>

    <x-filament-media-library::attachment :attachment="$item" />
</label>
