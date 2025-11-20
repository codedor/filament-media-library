<?php

namespace Codedor\MediaLibrary\Rules;

use Closure;
use Codedor\MediaLibrary\Support\FileUploadConfig;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileRule implements ValidationRule
{
    public function __construct(
        protected ?FileUploadConfig $fileUploadConfig = null
    ) {
        $this->fileUploadConfig ??= app(FileUploadConfig::class);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->validateImageDimensions($value, $fail);
        $this->validateImageFileSize($value, $fail);
        $this->validateFileSize($value, $fail);
        $this->validateExtension($value, $fail);
        $this->validateColorType($value, $fail);
    }

    public function validateImageDimensions(UploadedFile $value, Closure $fail)
    {
        if (! $value->isImage()) {
            return true;
        }

        $imageSize = getimagesize($value->getRealPath());
        $maxWidth = config('filament-media-library.validation.max_width', 4000);
        $maxHeight = config('filament-media-library.validation.max_height', 2400);

        if (
            $imageSize &&
            ($imageSize[0] > $maxWidth ||
                $imageSize[1] > $maxHeight)
        ) {
            $fail("File `{$value->getClientOriginalName()}` has the dimensions of {$imageSize[0]}x{$imageSize[1]} which is greater than the maximum allowed {$maxWidth}x{$maxHeight}");
        }
    }

    public function validateImageFileSize(UploadedFile $value, Closure $fail)
    {
        $maxImageFileSize = config('filament-media-library.validation.max_file_size', 5) * 1024;

        if (
            $value->isImage() &&
            $value->getSize() > $maxImageFileSize
        ) {
            $maxFilesize = Number::fileSize($maxImageFileSize);
            $currentFilesize = Number::fileSize($value->getSize());

            $fail("File `{$value->getClientOriginalName()}` has a size of {$currentFilesize} which is greater than the maximum allowed {$maxFilesize}");
        }
    }

    private function validateFileSize(UploadedFile $value, Closure $fail)
    {
        $maxFilesize = $this->fileUploadConfig->getMaxFilesize();

        if ($value->getSize() > $maxFilesize) {
            $currentFilesize = Number::fileSize($value->getSize());
            $maxFilesizeFormatted = Number::fileSize($maxFilesize);

            $fail("File `{$value->getClientOriginalName()}` has a size of {$currentFilesize} which is greater than the maximum allowed {$maxFilesizeFormatted}");
        }
    }

    public function validateExtension(UploadedFile $value, Closure $fail)
    {
        $allowedExtensions = collect(config('filament-media-library.extensions'))->flatten();
        $fileExtension = $value->guessExtension();

        if ($allowedExtensions->doesntContain($fileExtension)) {
            $fail("File `{$value->getClientOriginalName()}` has a not allowed extension of {$fileExtension}");
        }
    }

    public function validateColorType(UploadedFile $value, Closure $fail)
    {
        if (! $value->isImage()) {
            return true;
        }

        $imageInfo = getimagesize($value->getRealPath());

        if (! $imageInfo || ! isset($imageInfo['channels'])) {
            return true;
        }

        if ($imageInfo['channels'] !== 3) {
            $fail("Image `{$value->getClientOriginalName()}` must be RGB and not CMYK");
        }
    }
}
