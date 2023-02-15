<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

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
        'translated_name',
        'alt',
        'caption',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function (Attachment $attachment) {
            Storage::disk($attachment->disk)->deleteDirectory($attachment->directory());
        });
    }

    public function directory(): string
    {
        return "attachments/{$this->{$this->getKeyName()}}";
    }

    protected static function newFactory()
    {
        return AttachmentFactory::new();
    }

    public function scopeSearch(Builder $query, string $search = ''): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where('name', 'like', "%$search%");
    }

    public function url(): string
    {
        return Storage::disk($this->disk)
            ->url($this->directory() . '/' . $this->filename());
    }

    public function filename(): string
    {
        return "$this->name.$this->extension";
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(AttachmentTag::class);
    }
}
