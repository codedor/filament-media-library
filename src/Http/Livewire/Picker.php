<?php

namespace Codedor\Attachments\Http\Livewire;

use Codedor\Attachments\Models\Attachment;
use Codedor\Attachments\Models\AttachmentTag;
use Filament\Forms\Components as Fields;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Livewire\WithPagination;

class Picker extends Component implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    public string $statePath;

    public bool $isMultiple;

    public array $attachmentsList;

    public array $selected = [];

    public array $filters = [
        'query' => '',
        'tags' => [],
    ];

    protected $listeners = [
        'laravel-attachments::open-picker' => 'openPicker',
    ];

    public function render()
    {
        $ids = collect($this->attachmentsList)
            ->map(fn ($id) => "'{$id}'")
            ->join(',');

        $attachments = Attachment::query()
            ->orderByRaw("FIELD(id,{$ids})")
            ->whereIn('id', $this->attachmentsList)
            ->when(filled($this->filters['query']), function ($query) {
                $query->where('name', 'like', "%{$this->filters['query']}%");
            })
            ->when(filled($this->filters['tags']), function ($query) {
                $query->whereHas('tags', function ($query) {
                    $query->whereIn('id', $this->filters['tags']);
                });
            })
            ->paginate(18);

        return view('laravel-attachments::livewire.picker', [
            'attachments' => $attachments,
        ]);
    }

    public function openPicker(array $params = [])
    {
        if (($params['statePath'] ?? '') !== $this->statePath) {
            return;
        }

        $this->selected = $params['attachments'] ?? [];
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filters = [
            'query' => '',
            'tags' => [],
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Fields\Grid::make(2)->schema([
                Fields\TextInput::make('filters.query')
                    ->placeholder(__('filament_medias::filters search'))
                    ->disableLabel(true)
                    ->reactive(),

                Fields\Select::make('filters.tags')
                    ->options(AttachmentTag::limit(50)->pluck('title', 'id'))
                    ->disableLabel(true)
                    ->searchable()
                    ->multiple()
                    ->reactive(),
            ]),
        ];
    }
}
