<?php

namespace Codedor\Attachments\Components\Fields;

use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class AttachmentInput extends Field implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'laravel-attachments::components.fields.attachment-input';
}
