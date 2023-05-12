<?php

namespace Codedor\Attachments\Models;

use Codedor\Attachments\Database\Factories\AttachmentFactory;
use Codedor\Attachments\Exceptions\FormatNotFound;
use Codedor\Attachments\Facades\Formats;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
            $attachment->getStorage()->deleteDirectory($attachment->directory);
        });
    }

    public function getStorage(): Filesystem
    {
        return Storage::disk($this->disk);
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

    public function getUrlAttribute(): string
    {
        return $this->getStorage()
            ->url("{$this->directory}/{$this->filename}");
    }

    public function getFilenameAttribute(): string
    {
        return "{$this->name}.{$this->extension}";
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(AttachmentTag::class);
    }

    public function formats(): HasMany
    {
        return $this->hasMany(AttachmentFormat::class);
    }

    public function getRootDirectoryAttribute(): string
    {
        return 'attachments';
    }

    public function getDirectoryAttribute(): string
    {
        return "{$this->root_directory}/{$this->id}";
    }

    public function getFilePathAttribute(): string
    {
        return "{$this->directory}/{$this->filename}";
    }

    public function getFormatOrOriginal(?string $name): string
    {
        if (! $name) {
            return $this->url;
        }

        return $this->getFormat($name) ?: $this->url;
    }

    public function getFormat(string $name): string|null
    {
        $format = Formats::exists($name);

        if (! $format) {
            FormatNotFound::throw($name);

            return null;
        }

        return $this->getStorage()->url("{$this->directory}/{$format->filename($this)}");
    }

    public function getAbsoluteDirectoryPathAttribute(): string
    {
        return $this->getStorage()->path($this->directory);
    }

    public function getAbsoluteFilePathAttribute(): string
    {
        return $this->getStorage()->path($this->file_path);
    }

    public function getFormattedInMbSizeAttribute(): string
    {
        return round($this->size / 1000000, 2);
    }
}
