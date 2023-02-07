<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;
    use HasUlids;

    protected $keyType = 'string';

    protected $fillable = [
        'extension',
        'mime_type',
        'md5',
        'type',
        'size',
        'width',
        'height',
        'disk',
        'name',
    ];

    protected static function newFactory()
    {
        return AttachmentFactory::new();
    }

    public function url(): string
    {
        return Storage::disk($this->disk)
            ->url($this->directory() . '/' . $this->filename());
    }

    public function directory(): string
    {
        return "attachments/{$this->{$this->getKeyName()}}";
    }

    public function filename(): string
    {
        return "$this->name.$this->extension";
    }
}
