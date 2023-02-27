# Laravel Attachments for Filament

| Table Of Content              |
|-------------------------------|
| [Installation](#installation) |

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

