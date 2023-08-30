<?php

namespace Codedor\MediaLibrary\Models;

use Carbon\Carbon;
use Codedor\MediaLibrary\Database\Factories\AttachmentFactory;
use Codedor\MediaLibrary\Models\Traits\HasFormats;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $name
 * @property Carbon $created_at
 * @property string $mime_type
 * @property string $type
 * @property string $disk
 * @property string $md5
 * @property int $size
 * @property string $extension
 * @property int|null $width
 * @property int|null $height
 */
class Attachment extends Model
{
    use HasFactory;
    use HasFormats;
    use HasTranslations;
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

    protected $translatable = [
        'translated_name',
        'alt',
        'caption',
    ];

    protected static function booted()
    {
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
        return (string) round($this->size / 1000000, 2);
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }
}
