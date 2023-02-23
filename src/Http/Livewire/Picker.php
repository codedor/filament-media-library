<?php

namespace Codedor\Attachments\Http\Livewire;

use Codedor\Attachments\Models\Attachment;
use Livewire\Component;
use Livewire\WithPagination;

class Picker extends Component
{
    use WithPagination;

    public array $selectedAttachments = [];

    public string $statePath;

    public string $search = '';

    public bool $multiple = false;

    public function selectAttachments()
    {
        $this->dispatchBrowserEvent("laravel-attachment::picked-{$this->statePath}", [
            'value' => $this->multiple
               ? $this->selectedAttachments
               : $this->selectedAttachments[0] ?? null,
        ]);
    }

    public function render()
    {
        return view('laravel-attachments::livewire.picker', [
            'attachments' => Attachment::query()
                ->search($this->search)
                ->paginate(18),
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
