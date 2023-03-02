<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Attachment extends Model
{
    use HasFactory;
    use HasUuids;
    use HasTranslations;

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

    protected $translatable = [
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

    public function getFilenameAttribute(): string
    {
        return "$this->name.$this->extension";
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(AttachmentTag::class);
    }

    public function getRootDirectoryAttribute(): string
    {
        return 'attachments';
    }

    public function getDirectoryAttribute(): string
    {
        return "$this->root_directory/$this->id";
    }

    public function getFilePathAttribute(): string
    {
        return "$this->directory/$this->filename";
    }

    public function getAbsoluteDirectoryPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->directory);
    }

    public function getAbsoluteFilePathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }
}
