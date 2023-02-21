<?php

namespace Codedor\Attachments\Http\Livewire;

use Codedor\Attachments\Models\Attachment;
use Livewire\Component;
use Livewire\WithPagination;

class Picker extends Component
{
    use WithPagination;

    public array $selectedAttachments = [];

    public $search = '';

    protected $queryString = [
        'search' => ['except' => '', 'as' => 's'],
    ];

    protected $listeners = ['modal-closed' => 'test'];

    public function toggle(string $id)
    {
        if (in_array($id, $this->selectedAttachments)) {
            $this->selectedAttachments = array_diff($this->selectedAttachments, [$id]);
        } else {
            $this->selectedAttachments[] = $id;
        }
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

    public function getQueryString()
    {
        return [];
    }
}
