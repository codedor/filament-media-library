# Laravel Attachments for Filament

## Installation

First, install this package via composer:

```bash
composer require codedor/laravel-attachments
```

Then publish the assets with

```bash
php artisan vendor:publish --provider "Codedor\Attachments\Providers\AttachmentServiceProvider"
```

and lastly, run the migrations:

```bash
php artisan migrate
```

## Configuration

The basic config file consists of the following contents:

```php
<?php

use Codedor\Attachments\Facades\Models;

return [
    'extensions' => [
        'image' => [
            'jpg',
            'jpeg',
            'svg',
            'png',
            'webp',
            'gif',
        ],
        'document' => [
            'txt',
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'zip',
            'odf',
        ],
        'video' => [
            'mp4',
            'm4v',
            'webm',
            'ogg',
        ],
    ],
    'models' => Models::add(),
];

```

### Extensions

This package divides files into 3 different types. An image, document, video and other. The config can define which file
extensions belong to which type.

This configuration also decides which files can be uploaded. An extension that is not defined is not going to be able to
be uploaded.

This configuration can be adjusted as desired.

## Formats

### Registering the models

Formats are defined by model.

Any model that contains formats should be registered. These can be added with

```php
use Codedor\Attachments\Facades\Formats;

public function boot()
{
    ...
    Models::add([BlogPost::class]);
}
```
