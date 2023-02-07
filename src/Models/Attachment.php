<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attachment extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attachment) {
            $attachment->id = (string) Str::uuid();
        });
    }

    protected static function newFactory()
    {
        return AttachmentFactory::new();
    }
}
