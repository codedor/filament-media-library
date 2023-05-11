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

Follow the [Formats](##formats) section to create and use formats.

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

Formats are tightly coupled with models. This way, specific formats can be fetched per model to prevent overhead.

#### Preparing your model

A model should implement the `Codedor\Attachments\Interfaces\HasFormats` interface which contains the `getFormats`
method.

```php
public static function getFormats(Collection $formats): Collection
{
    return $formats->add(Hero::make('attachment_id'))
        ->add(...);
}
```

## Attachment model methods and attributes

### Methods

#### getStorage

Retrieve the Filesystem of the attachment

```php
use Codedor\Attachments\Models\Attachment;

/** @var Illuminate\Contracts\Filesystem $filesystem */
$filesystem = Attachment::first()->getStorage();
```

#### getFormatOrOriginal

Retrieve the url for the file of the given format. Returns the original file if the given format is not found.

```php
use Codedor\Attachments\Models\Attachment;

/** @var string $url */
$url = Attachment::first()->getFormatOrOriginal('hero');

// https://example.com/storage/{root_directory}/{attachment_id}/{snaked_format_name}__{filename}
// or
// https://example.com/storage/{root_directory}/{attachment_id}/{filename}
```

#### getFormat

Retrieve the url for the file of the given format. Returns null if the given format is not found.

```php
use Codedor\Attachments\Models\Attachment;

/** @var string|null $url */
$url = Attachment::first()->getFormat('hero');

// https://example.com/storage/{root_directory}/{attachment_id}/{snaked_format_name}__{filename}
```

### Attributes

#### url

Retrieve the url to the original file

```php
use Codedor\Attachments\Models\Attachment;

/** @var string $url */
$url = Attachment::first()->url;

// https://example.com/storage/{root_directory}/{attachment_id}/{filename}
```

#### filename

Retrieve the filename with extension.

```php
use Codedor\Attachments\Models\Attachment;

/** @var string $url */
$url = Attachment::first()->filename;

// image.jpeg
```

#### root_directory

Retrieve the root directory in which the folder structure and files should be saved. This defaults to `attachments`

#### directory

Retrieve the relative path to the directory where all the formats and the original file is located.

`{root_directory}/{attachment_id}`

#### file_path

Retrieve the relative path to the original file.

`{root_directory}/{attachment_id}/{file_name}`

#### absolute_directory_path

Retrieve the full path to the attachment directory.

`{path_to_storage_folder}/{root_directory}/{attachment_id}`

#### absolute_file_path

Retrieve the full path to the original file.

`{path_to_storage_folder}/{root_directory}/{attachment_id}/{file_name}`

## Usage in Blade

## Usage in Filament
