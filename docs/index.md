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

### Register models

Any model that contains formats should be registered and implement the `Codedor\Attachments\Interfaces\HasFormats`
interface.

Models can be registered via the `Codedor\Attachments\Facades\Models` facade.

```php
use App\Models\BlogPost;
use App\Models\NewsItem;
use Codedor\Attachments\Facades\Models;

public function boot()
{
    ...
    Models::add(BlogPost::class)
        ->add(NewsItem::class);
}
```

### Creating formats

Create a new PHP class that extends the `Codedor\Attachments\Formats\Format` class.

```php
<?php

namespace App\Formats;

use Codedor\Attachments\Entities\Manipulations;

class Hero extends Format
{
    protected string $description = 'This format is used for hero images';

    public function definition(): Manipulations
    {
        return $this->manipulations()
            ->blur(3)
            ...;
    }
}
```

#### Format definition

The manipulations object has predefined methods that will perform manipulations on an image.

These manipulations are based of Glide. A reference can be found in
the [Glide docs](https://glide.thephpleague.com/2.0/api/quick-reference/)

### Registering formats

Formats are tightly coupled with models.
