<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    use HasUuids;

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

    public function filename(): string
    {
        return "$this->name.$this->extension";
    }

    public function directory(): string
    {
        return "attachments/{$this->{$this->getKeyName()}}";
    }
}
