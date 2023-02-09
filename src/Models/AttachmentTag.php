<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentTagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AttachmentTag::class, 'parent_attachment_tag_id');
    }
}
