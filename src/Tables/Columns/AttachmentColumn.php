<?php

namespace Codedor\Attachments\Tables\Columns;

use Closure;
use Codedor\Attachments\Models\Attachment;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns;
use Illuminate\Support\Collection;

class AttachmentColumn extends Column
{
    use Concerns\HasDescription;

    protected string $view = 'laravel-attachments::components.columns.attachments-column';

    public int|Closure $limit = 3;

    public function getState()
    {
        $state = parent::getState();

        if (is_string($state)) {
            $state = Attachment::find($state);
        }

        $state = Collection::wrap($state);

        // Fetch the items again, otherwise we'll not have access to all our data
        return Attachment::whereIn('id', $state->pluck('id'))->get();
    }

    public function limit(int|Closure $limit): self
    {
        $this->limit = ($limit === -1) ? PHP_INT_MAX : $limit;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->evaluate($this->limit);
    }
}
