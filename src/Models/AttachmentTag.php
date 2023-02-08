<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    protected static function newFactory()
    {
        return AttachmentTagFactory::new();
    }
}
