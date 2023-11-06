# Media Library for Filament

- [Installation](#installation)
- [Configuration](#configuration)
    * [Extensions](#extensions)
- [Formats](#formats)
    * [Register models](#register-models)
    * [Creating formats](#creating-formats)
        + [Format definition](#format-definition)
    * [Registering formats](#registering-formats)
        + [Preparing your model](#preparing-your-model)
- [Attachment model methods and attributes](#attachment-model-methods-and-attributes)
    * [Methods](#methods)
        + [getStorage](#getstorage)
        + [getFormatOrOriginal](#getformatororiginal)
        + [getFormat](#getformat)
    * [Attributes](#attributes)
        + [url](#url)
        + [filename](#filename)
        + [root_directory](#root-directory)
        + [directory](#directory)
        + [file_path](#file-path)
        + [absolute_directory_path](#absolute-directory-path)
        + [absolute_file_path](#absolute-file-path)
- [Usage in Blade](#usage-in-blade)
- [Usage in Filament](#usage-in-filament)
    * [AttachmentInput](#attachmentinput)
      * [Multiple attachments](#multiple-attachments)
      * [allowedFormats](#allowedformats)
    * [AttachmentColumn](#attachmentcolumn)
    * [AttachmentEntry](#attachmententry)
- [UploadedFile Mixin]('#uploaded-file-mixin)
    * [Save Attachment]('#save-attachment)
    * [Create from URL]('#create-from-url)

## Installation

First, install this package via composer:

```bash
composer require codedor/filament-media-library
```

Then publish the assets with

```bash
php artisan vendor:publish --provider "Codedor\MediaLibrary\Providers\MediaLibraryServiceProvider"
```

and lastly, run the migrations:

```bash
php artisan migrate
```

Follow the [Formats](##formats) section to create and use formats.

## Configuration

The basic config file consists of the following contents:

```php
return [
    'conversion' => \Codedor\MediaLibrary\Conversions\LocalConversion::class,
    'enable-format-generate-action' => true,
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
];


```

### Extensions

This package divides files into 3 different types. An image, document, video and other. The config can define which file
extensions belong to which type.

This configuration also decides which files can be uploaded. An extension that is not defined is not going to be able to
be uploaded.

This configuration can be adjusted as desired.

### Showing the format generation action

The format generation action is a button that will generate all the formats for the given attachment.
This can be used on the Media Library as a bulk action. This action can be disabled by setting the `enable-format-generate-action` to false.

## Formats

### Register models

Any model that contains formats should be registered and implement the `Codedor\MediaLibrary\Interfaces\HasFormats`
interface.

Formats must be registered via the `Codedor\MediaLibrary\Facades\Formats` facade to be visible in the formatter.

```php
use App\Formats;
use Codedor\MediaLibrary\Facades\Formats;

public function boot()
{
    ...

    Formats::register([
        Formats\Thumbnail::class,
        Formats\FullWidth::class,
    ]);
}
```

### Creating formats

Create a new PHP class that extends the `Codedor\MediaLibrary\Formats\Format` class.

```php
<?php

namespace App\Formats;

use Spatie\Image\Manipulations;

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

A model should implement the `Codedor\MediaLibrary\Interfaces\HasFormats` interface which contains the `getFormats`
method.

```php
public static function getFormats(Collection $formats): Collection
{
    return $formats->add(Hero::make('attachment_id'))
        ->add(...);
}
```

Formats will be available in kebab-case format to call them. So the `HeroHeader` class becomes `header-hero`.

## Attachment model methods and attributes

### Methods

#### getStorage

Retrieve the Filesystem of the attachment

```php
use Codedor\MediaLibrary\Models\Attachment;

/** @var Illuminate\Contracts\Filesystem $filesystem */
$filesystem = Attachment::first()->getStorage();
```

#### getFormatOrOriginal

Retrieve the url for the file of the given format. Returns the original file if the given format is not found.

```php
use Codedor\MediaLibrary\Models\Attachment;

/** @var string $url */
$url = Attachment::first()->getFormatOrOriginal('hero');

// https://example.com/storage/{root_directory}/{attachment_id}/{snaked_format_name}__{filename}
// or
// https://example.com/storage/{root_directory}/{attachment_id}/{filename}
```

#### getFormat

Retrieve the url for the file of the given format. Returns null if the given format is not found.

```php
use Codedor\MediaLibrary\Models\Attachment;

/** @var string|null $url */
$url = Attachment::first()->getFormat('hero');

// https://example.com/storage/{root_directory}/{attachment_id}/{snaked_format_name}__{filename}
```

### Attributes

#### url

Retrieve the url to the original file

```php
use Codedor\MediaLibrary\Models\Attachment;

/** @var string $url */
$url = Attachment::first()->url;

// https://example.com/storage/{root_directory}/{attachment_id}/{filename}
```

#### filename

Retrieve the filename with extension.

```php
use Codedor\MediaLibrary\Models\Attachment;

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

This package provides a `<x-filament-media-library::picture />` component which will render the provided attachment with the given format. If no format is defined, the original attachment will be rendered.

```php
<x-filament-media-library::picture
    :image="$attachment"
    format="thumb"
    alt="alt text"
    class="img"
>
    <p>Filament Media Library package!</p>
</x-filament-media-library::picture>
```

Will return

```html
<picture class="img">
    <p>Filament Media Library package!</p>
    <source type="{MIME_TYPE}" srcset="{{IMAGE_SRC}}" />
    <img alt="alt text" src="{{IMAGE_SRC}}" />
</picture>
```

## Usage in Filament

### AttachmentInput

This field will give the option to upload or select an already existing image.
The ID will be stored in de column provided in the `make` method.

```php
use Codedor\MediaLibrary\Components\Fields\AttachmentInput;

AttachmentInput::make('profile_image_id')
    ->label('Profile Image')
```

This field inherits the `Filament\Forms\Components\Field` class which means that this field can do all the things other fields can do too.

#### Multiple attachments

```php
use Codedor\MediaLibrary\Components\Fields\AttachmentInput;

AttachmentInput::make('profile_image_id')
    ->multiple()
```

#### allowedFormats

The allowed formats in the cropper are based on the `getFormats` method in the model.
If you want to override this, you can use the `allowedFormats` method.

```php
use App\Formats\Hero;
use Codedor\MediaLibrary\Components\Fields\AttachmentInput;

AttachmentInput::make('profile_image_id')
    ->allowedFormats([
        Hero::make()
    ])
```

### AttachmentColumn

This column for a table will render the image with the thumbnail format or an icon if attachment is not an image.

```php
\Codedor\MediaLibrary\Tables\Columns\AttachmentColumn::make('image_id'),
```

### AttachmentColumn

This entry for an info list will render the image with the thumbnail format or an icon if attachment is not an image.

```php
\Codedor\MediaLibrary\Filament\Entries\AttachmentEntry::make('image'),
```


## UploadedFile Mixin

We add some methods to the UploadedFile class to make it easier to work with our attachments.

### Save Attachment

To convert an uploaded file to an attachment, you simply call the `save()` method on the UploadedFile.

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

class UploadPhoto extends Component
{
    use WithFileUploads;

    #[Rule('image|max:1024')] // 1MB Max
    public $photo;

    public function save()
    {
        $this->photo->save();
    }
}
```

### Create from URL

To convert an url to an attachment, you simply call the `createFromUrl()` method on the UploadedFile.

```php
$uploadedFile = \Illuminate\Http\UploadedFile::createFromUrl('https://example.com/image.jpg');

$attachment = $uploadedFile->save();
```
